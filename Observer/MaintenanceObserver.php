<?php
namespace Mavenbird\MaintenancePage\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool as CacheFrontendPool;
use Mavenbird\MaintenancePage\Helper\Data as MaintenanceHelper;

class MaintenanceObserver implements ObserverInterface
{
    private MaintenanceHelper $helper;
    private HttpResponse $response;
    private RequestInterface $request;
    private UrlInterface $url;
    private WriterInterface $configWriter;
    private TypeListInterface $cacheTypeList;
    private CacheFrontendPool $cacheFrontendPool;

    public function __construct(
        MaintenanceHelper $helper,
        HttpResponse $response,
        RequestInterface $request,
        UrlInterface $url,
        WriterInterface $configWriter,
        TypeListInterface $cacheTypeList,
        CacheFrontendPool $cacheFrontendPool
    ) {
        $this->helper            = $helper;
        $this->response          = $response;
        $this->request           = $request;
        $this->url               = $url;
        $this->configWriter      = $configWriter;
        $this->cacheTypeList     = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
    }

    public function execute(Observer $observer): void
    {
        $pathInfo = trim($this->request->getPathInfo(), '/');

        // The ACTUAL browser URL (before Magento URL rewrite resolves it)
        // e.g. "gear/bags.html" or "men/bottoms-men.html"
        $requestUri = trim(
            strtok(trim($_SERVER['REQUEST_URI'] ?? $this->request->getRequestUri(), '/'), '?'),
            '/'
        );

        $isOnMaintenancePage = (
            strpos($pathInfo, 'maintenance/index/index') === 0 ||
            strpos($pathInfo, 'maintenance/index/comingsoon') === 0
        );

        // ----------------------------------------------------------------
        // STEP 1: Auto-disable when countdown timer expires.
        // ----------------------------------------------------------------
        if (
            $this->helper->isEnabled()
            && $this->helper->isAutoSwitch()
            && $this->helper->isEndDateTimePassed()
        ) {
            $this->configWriter->save(MaintenanceHelper::XML_GENERAL_ENABLED, 0);
            foreach ($this->cacheTypeList->getTypes() as $type) {
                $this->cacheTypeList->cleanType($type->getId());
            }
            foreach ($this->cacheFrontendPool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
            if ($isOnMaintenancePage) {
                $this->response->setRedirect($this->url->getBaseUrl(), 302);
                $this->response->sendResponse();
                exit;
            }
            return;
        }

        // ----------------------------------------------------------------
        // STEP 2: Module disabled → bounce off maintenance page to homepage.
        // ----------------------------------------------------------------
        if (!$this->helper->isEnabled()) {
            if ($isOnMaintenancePage) {
                $this->response->setRedirect($this->url->getBaseUrl(), 302);
                $this->response->sendResponse();
                exit;
            }
            return;
        }

        // ----------------------------------------------------------------
        // STEP 3: Module is ON — decide whether to redirect.
        // ----------------------------------------------------------------
        if ($this->isAdminRoute()) {
            return;
        }
        if ($this->isAllowedRoute($pathInfo)) {
            return;
        }
        if ($this->helper->isIpWhitelisted($this->getClientIp())) {
            return;
        }

        // Check whitelist against BOTH the real browser URI and the internal pathInfo
        if ($this->isWhitelistedLink($requestUri, $pathInfo)) {
            return;
        }
        if ($isOnMaintenancePage) {
            return;
        }

        // ----------------------------------------------------------------
        // STEP 4: Redirect to maintenance or coming soon page
        // ----------------------------------------------------------------
        $redirectPage = $this->helper->getRedirectToPage();
        $redirectUrl  = $redirectPage === 'coming_soon'
            ? $this->url->getUrl('maintenance/index/comingsoon')
            : $this->url->getUrl('maintenance/index/index');

        $this->response->setRedirect($redirectUrl, 302);
        $this->response->sendResponse();
        exit;
    }

    private function isAdminRoute(): bool
    {
        $pathInfo   = trim($this->request->getPathInfo(), '/');
        $moduleName = $this->request->getModuleName();
        if (in_array($moduleName, ['adminhtml', 'backend'])) {
            return true;
        }
        foreach (['admin', 'backend', 'adminhtml'] as $prefix) {
            if (strpos($pathInfo, $prefix) === 0) {
                return true;
            }
        }
        return false;
    }

    private function isAllowedRoute(string $pathInfo): bool
    {
        foreach (['pub/static', 'pub/media', 'static', 'media', 'rest', 'graphql'] as $prefix) {
            if (strpos($pathInfo, $prefix) === 0) {
                return true;
            }
        }
        if ($this->request->getParam('preview') === '1') {
            return true;
        }
        return false;
    }

    /**
     * Normalize a path for comparison:
     * - trim slashes
     * - lowercase
     * - strip .html suffix
     */
    private function normalizePath(string $path): string
    {
        $path = strtolower(trim($path, '/'));
        if (substr($path, -5) === '.html') {
            $path = substr($path, 0, -5);
        }
        return $path;
    }

    /**
     * Check if the current request matches a whitelisted page link.
     *
     * KEY INSIGHT from debug log:
     *   Browser visits: https://app.magento248.test/gear/bags.html
     *   $requestUri (REQUEST_URI) = "gear/bags.html"   ← the real browser URL
     *   $pathInfo                 = "catalog/category/view/id/13"  ← internal route after URL rewrite
     *
     * We must check against REQUEST_URI (the pretty URL the user typed),
     * NOT pathInfo (which is already rewritten internally by Magento).
     * We keep pathInfo check as fallback for routes without URL rewrites.
     *
     * Supported admin input formats (one per line):
     *   - "/"                                       → homepage
     *   - "about-us"                                → relative path
     *   - "gear/bags.html"                          → relative path with .html
     *   - "https://example.com/gear/bags.html"      → full URL
     *   - "https://example.com/"                    → homepage
     */
    private function isWhitelistedLink(string $requestUri, string $pathInfo): bool
    {
        $links = $this->helper->getWhitelistPageLinks();
        if (empty($links)) {
            return false;
        }

        $normalizedRequestUri = $this->normalizePath($requestUri);
        $normalizedPathInfo   = $this->normalizePath($pathInfo);

        // Homepage detection
        $isHomepage = ($requestUri === '' || $normalizedRequestUri === '' || $normalizedPathInfo === 'cms/index/index');

        // Strip store base path (for subdirectory installs)
        $baseUrl  = $this->url->getBaseUrl();
        $basePath = trim((string)parse_url($baseUrl, PHP_URL_PATH), '/');

        foreach ($links as $link) {
            $link = trim($link);
            if (empty($link)) {
                continue;
            }

            // ---- Case 1: plain "/" → homepage only ----
            if ($link === '/') {
                if ($isHomepage) {
                    return true;
                }
                continue;
            }

            // ---- Case 2: full URL (e.g. https://example.com/gear/bags.html) ----
            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $parsedPath = trim((string)parse_url($link, PHP_URL_PATH), '/');

                // Strip base path for subdirectory installs
                if ($basePath !== '' && strpos($parsedPath, $basePath . '/') === 0) {
                    $parsedPath = trim(substr($parsedPath, strlen($basePath)), '/');
                }

                // Empty path = homepage
                if ($parsedPath === '') {
                    if ($isHomepage) {
                        return true;
                    }
                    continue;
                }

                $normalizedLinkPath = $this->normalizePath($parsedPath);

                // Match against REQUEST_URI (pretty URL) — primary check
                if ($normalizedRequestUri === $normalizedLinkPath) {
                    return true;
                }

                // Match against internal pathInfo — fallback for non-rewritten routes
                if ($normalizedPathInfo === $normalizedLinkPath) {
                    return true;
                }

                continue;
            }

            // ---- Case 3: relative path (e.g. "gear/bags.html" or "about-us") ----
            $linkPath           = trim((string)(parse_url($link, PHP_URL_PATH) ?? $link), '/');
            $normalizedLinkPath = $this->normalizePath($linkPath);

            if ($linkPath === '') {
                continue;
            }

            // Match against REQUEST_URI first, then pathInfo as fallback
            if ($normalizedRequestUri === $normalizedLinkPath) {
                return true;
            }
            if ($normalizedPathInfo === $normalizedLinkPath) {
                return true;
            }
        }

        return false;
    }

    private function getClientIp(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return (string)$this->request->getClientIp();
    }
}