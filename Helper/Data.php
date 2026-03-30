<?php
namespace Mavenbird\MaintenancePage\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_GENERAL_ENABLED          = 'mavenbird_maintenancepage/general/enabled';
    const XML_GENERAL_REDIRECT_TO_PAGE = 'mavenbird_maintenancepage/general/redirect_to_page';
    const XML_GENERAL_END_DATE_TIME    = 'mavenbird_maintenancepage/general/end_date_time';
    const XML_GENERAL_AUTO_SWITCH      = 'mavenbird_maintenancepage/general/auto_switch';
    const XML_GENERAL_WHITELIST_IPS    = 'mavenbird_maintenancepage/general/whitelist_ips';
    const XML_GENERAL_WHITELIST_LINKS  = 'mavenbird_maintenancepage/general/whitelist_page_links';
    const XML_GENERAL_HTTP_RESPONSE    = 'mavenbird_maintenancepage/general/http_response_header';

    const XML_MAINTENANCE_TITLE            = 'mavenbird_maintenancepage/maintenance_page/page_title';
    const XML_MAINTENANCE_DESCRIPTION      = 'mavenbird_maintenancepage/maintenance_page/page_description';
    const XML_MAINTENANCE_TEXT_COLOR       = 'mavenbird_maintenancepage/maintenance_page/text_color';
    const XML_MAINTENANCE_LOGO             = 'mavenbird_maintenancepage/maintenance_page/logo_url';
    const XML_MAINTENANCE_BG_TYPE          = 'mavenbird_maintenancepage/maintenance_page/background_type';
    const XML_MAINTENANCE_BG_COLOR         = 'mavenbird_maintenancepage/maintenance_page/background_color';
    const XML_MAINTENANCE_BG_IMAGE         = 'mavenbird_maintenancepage/maintenance_page/background_image';
    const XML_MAINTENANCE_BG_VIDEO         = 'mavenbird_maintenancepage/maintenance_page/background_video';
    const XML_MAINTENANCE_PROGRESS_ENABLED = 'mavenbird_maintenancepage/maintenance_page/progress_bar_enabled';
    const XML_MAINTENANCE_PROGRESS_VALUE   = 'mavenbird_maintenancepage/maintenance_page/progress_bar_value';
    const XML_MAINTENANCE_PROGRESS_COLOR   = 'mavenbird_maintenancepage/maintenance_page/progress_bar_color';

    const XML_COMINGSOON_TITLE             = 'mavenbird_maintenancepage/coming_soon_page/page_title';
    const XML_COMINGSOON_DESCRIPTION       = 'mavenbird_maintenancepage/coming_soon_page/page_description';
    const XML_COMINGSOON_TEXT_COLOR        = 'mavenbird_maintenancepage/coming_soon_page/text_color';
    const XML_COMINGSOON_LOGO              = 'mavenbird_maintenancepage/coming_soon_page/logo_url';
    const XML_COMINGSOON_BG_TYPE           = 'mavenbird_maintenancepage/coming_soon_page/background_type';
    const XML_COMINGSOON_BG_COLOR          = 'mavenbird_maintenancepage/coming_soon_page/background_color';
    const XML_COMINGSOON_BG_IMAGE          = 'mavenbird_maintenancepage/coming_soon_page/background_image';
    const XML_COMINGSOON_BG_VIDEO          = 'mavenbird_maintenancepage/coming_soon_page/background_video';
    const XML_COMINGSOON_PROGRESS_ENABLED  = 'mavenbird_maintenancepage/coming_soon_page/progress_bar_enabled';
    const XML_COMINGSOON_PROGRESS_VALUE    = 'mavenbird_maintenancepage/coming_soon_page/progress_bar_value';
    const XML_COMINGSOON_PROGRESS_COLOR    = 'mavenbird_maintenancepage/coming_soon_page/progress_bar_color';

    const XML_CLOCK_ENABLED      = 'mavenbird_maintenancepage/clock/enabled';
    const XML_CLOCK_STYLE        = 'mavenbird_maintenancepage/clock/style';
    const XML_CLOCK_BG_COLOR     = 'mavenbird_maintenancepage/clock/background_color';
    const XML_CLOCK_NUMBER_COLOR = 'mavenbird_maintenancepage/clock/number_color';
    const XML_CLOCK_LABEL_COLOR  = 'mavenbird_maintenancepage/clock/label_color';

    const XML_SUBSCRIBE_ENABLED       = 'mavenbird_maintenancepage/subscribe/enabled';
    const XML_SUBSCRIBE_TYPE          = 'mavenbird_maintenancepage/subscribe/subscription_type';
    const XML_SUBSCRIBE_DESCRIPTION   = 'mavenbird_maintenancepage/subscribe/description';
    const XML_SUBSCRIBE_DESC_COLOR    = 'mavenbird_maintenancepage/subscribe/description_text_color';
    const XML_SUBSCRIBE_BTN_LABEL     = 'mavenbird_maintenancepage/subscribe/button_label';
    const XML_SUBSCRIBE_BTN_TXT_COLOR = 'mavenbird_maintenancepage/subscribe/button_text_color';
    const XML_SUBSCRIBE_BTN_BG_COLOR  = 'mavenbird_maintenancepage/subscribe/button_background_color';

    const XML_SOCIAL_ENABLED     = 'mavenbird_maintenancepage/social/enabled';
    const XML_SOCIAL_LABEL       = 'mavenbird_maintenancepage/social/label';
    const XML_SOCIAL_LABEL_COLOR = 'mavenbird_maintenancepage/social/label_color';
    const XML_SOCIAL_FACEBOOK    = 'mavenbird_maintenancepage/social/facebook';
    const XML_SOCIAL_TWITTER     = 'mavenbird_maintenancepage/social/twitter';
    const XML_SOCIAL_INSTAGRAM   = 'mavenbird_maintenancepage/social/instagram';
    const XML_SOCIAL_GOOGLE_PLUS = 'mavenbird_maintenancepage/social/google_plus';
    const XML_SOCIAL_YOUTUBE     = 'mavenbird_maintenancepage/social/youtube';
    const XML_SOCIAL_PINTEREST   = 'mavenbird_maintenancepage/social/pinterest';

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    private function getConfig(string $path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    // GENERAL
    public function isEnabled(): bool { return (bool)$this->getConfig(self::XML_GENERAL_ENABLED); }
    public function getRedirectToPage(): string { return (string)$this->getConfig(self::XML_GENERAL_REDIRECT_TO_PAGE); }
    public function getEndDateTime(): string { return (string)$this->getConfig(self::XML_GENERAL_END_DATE_TIME); }
    public function isAutoSwitch(): bool { return (bool)$this->getConfig(self::XML_GENERAL_AUTO_SWITCH); }
    public function getHttpResponseHeader(): string { return (string)$this->getConfig(self::XML_GENERAL_HTTP_RESPONSE); }

    /**
     * Returns cleaned array of whitelisted IPs.
     * Handles both comma-separated AND newline-separated values.
     * Strips \r (Windows line endings).
     *
     * Supported input formats:
     *   - "192.168.1.1, 10.0.0.1"        (comma-separated)
     *   - "192.168.1.1\n10.0.0.1"         (newline-separated)
     *   - "192.168.1.1\n10.0.0.1\n..."    (multiple newlines)
     */
    public function getWhitelistIps(): array
    {
        $raw = (string)$this->getConfig(self::XML_GENERAL_WHITELIST_IPS);
        if (empty(trim($raw))) {
            return [];
        }

        // Normalize: replace all newline variants with comma, then split by comma
        // This supports both comma-separated AND newline-separated IP entries
        $normalized = str_replace(["\r\n", "\r", "\n"], ',', $raw);

        return array_values(array_filter(
            array_map(
                fn($ip) => trim($ip),
                explode(',', $normalized)
            ),
            fn($ip) => !empty($ip)
        ));
    }

    /**
     * Check if the given IP is in the whitelist.
     * Supports exact match only (e.g. "192.168.1.1").
     * Each whitelisted IP must be valid — invalid entries are ignored.
     */
    public function isIpWhitelisted(string $clientIp): bool
    {
        if (empty($clientIp)) {
            return false;
        }

        foreach ($this->getWhitelistIps() as $entry) {
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            // Exact IP match (e.g. "192.168.1.100")
            if (filter_var($entry, FILTER_VALIDATE_IP) && $entry === $clientIp) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns cleaned array of whitelisted page links.
     * One link per line. Strips \r from Windows line endings.
     */
    public function getWhitelistPageLinks(): array
    {
        $raw = (string)$this->getConfig(self::XML_GENERAL_WHITELIST_LINKS);
        if (empty(trim($raw))) {
            return [];
        }
        return array_values(array_filter(
            array_map(
                fn($link) => trim(str_replace("\r", '', $link)),
                explode("\n", $raw)
            ),
            fn($link) => !empty($link)
        ));
    }

    // MAINTENANCE PAGE
    public function getMaintenanceTitle(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_TITLE); }
    public function getMaintenanceDescription(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_DESCRIPTION); }
    public function getMaintenanceTextColor(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_TEXT_COLOR); }
    public function getMaintenanceLogo(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_LOGO); }
    public function getMaintenanceBgType(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_BG_TYPE); }
    public function getMaintenanceBgColor(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_BG_COLOR); }
    public function getMaintenanceBgImage(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_BG_IMAGE); }
    public function getMaintenanceBgVideo(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_BG_VIDEO); }
    public function isMaintenanceProgressEnabled(): bool { return (bool)$this->getConfig(self::XML_MAINTENANCE_PROGRESS_ENABLED); }
    public function getMaintenanceProgressValue(): int { return (int)$this->getConfig(self::XML_MAINTENANCE_PROGRESS_VALUE); }
    public function getMaintenanceProgressColor(): string { return (string)$this->getConfig(self::XML_MAINTENANCE_PROGRESS_COLOR); }

    // COMING SOON PAGE
    public function getComingSoonTitle(): string { return (string)$this->getConfig(self::XML_COMINGSOON_TITLE); }
    public function getComingSoonDescription(): string { return (string)$this->getConfig(self::XML_COMINGSOON_DESCRIPTION); }
    public function getComingSoonTextColor(): string { return (string)$this->getConfig(self::XML_COMINGSOON_TEXT_COLOR); }
    public function getComingSoonLogo(): string { return (string)$this->getConfig(self::XML_COMINGSOON_LOGO); }
    public function getComingSoonBgType(): string { return (string)$this->getConfig(self::XML_COMINGSOON_BG_TYPE); }
    public function getComingSoonBgColor(): string { return (string)$this->getConfig(self::XML_COMINGSOON_BG_COLOR); }
    public function getComingSoonBgImage(): string { return (string)$this->getConfig(self::XML_COMINGSOON_BG_IMAGE); }
    public function getComingSoonBgVideo(): string { return (string)$this->getConfig(self::XML_COMINGSOON_BG_VIDEO); }
    public function isComingSoonProgressEnabled(): bool { return (bool)$this->getConfig(self::XML_COMINGSOON_PROGRESS_ENABLED); }
    public function getComingSoonProgressValue(): int { return (int)$this->getConfig(self::XML_COMINGSOON_PROGRESS_VALUE); }
    public function getComingSoonProgressColor(): string { return (string)$this->getConfig(self::XML_COMINGSOON_PROGRESS_COLOR); }

    // CLOCK
    public function isClockEnabled(): bool { return (bool)$this->getConfig(self::XML_CLOCK_ENABLED); }
    public function getClockStyle(): string { return (string)$this->getConfig(self::XML_CLOCK_STYLE); }
    public function getClockBgColor(): string { return (string)$this->getConfig(self::XML_CLOCK_BG_COLOR); }
    public function getClockNumberColor(): string { return (string)$this->getConfig(self::XML_CLOCK_NUMBER_COLOR); }
    public function getClockLabelColor(): string { return (string)$this->getConfig(self::XML_CLOCK_LABEL_COLOR); }

    // SUBSCRIBE
    public function isSubscribeEnabled(): bool { return (bool)$this->getConfig(self::XML_SUBSCRIBE_ENABLED); }
    public function getSubscriptionType(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_TYPE); }
    public function getSubscribeDescription(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_DESCRIPTION); }
    public function getSubscribeDescriptionColor(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_DESC_COLOR); }
    public function getSubscribeButtonLabel(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_BTN_LABEL); }
    public function getSubscribeButtonTextColor(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_BTN_TXT_COLOR); }
    public function getSubscribeButtonBgColor(): string { return (string)$this->getConfig(self::XML_SUBSCRIBE_BTN_BG_COLOR); }

    // SOCIAL
    public function isSocialEnabled(): bool { return (bool)$this->getConfig(self::XML_SOCIAL_ENABLED); }
    public function getSocialLabel(): string { return (string)$this->getConfig(self::XML_SOCIAL_LABEL); }
    public function getSocialLabelColor(): string { return (string)$this->getConfig(self::XML_SOCIAL_LABEL_COLOR); }
    public function getFacebookLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_FACEBOOK); }
    public function getTwitterLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_TWITTER); }
    public function getInstagramLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_INSTAGRAM); }
    public function getGooglePlusLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_GOOGLE_PLUS); }
    public function getYoutubeLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_YOUTUBE); }
    public function getPinterestLink(): string { return (string)$this->getConfig(self::XML_SOCIAL_PINTEREST); }

    public function getSocialLinks(): array
    {
        return array_filter([
            'facebook'  => $this->getFacebookLink(),
            'twitter'   => $this->getTwitterLink(),
            'instagram' => $this->getInstagramLink(),
            'google'    => $this->getGooglePlusLink(),
            'youtube'   => $this->getYoutubeLink(),
            'pinterest' => $this->getPinterestLink(),
        ], fn($url) => !empty(trim($url)));
    }

    public function getEndDateTimeTimestamp(): int
    {
        $endDateTime = $this->getEndDateTime();
        if (empty(trim($endDateTime))) {
            return 0;
        }
        $formats = ['m/d/Y H:i', 'm/d/Y H:i:s', 'Y-m-d H:i:s', 'Y-m-d H:i'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, trim($endDateTime));
            if ($date !== false) {
                return $date->getTimestamp();
            }
        }
        $ts = strtotime($endDateTime);
        return $ts > 0 ? $ts : 0;
    }

    public function isEndDateTimePassed(): bool
    {
        $ts = $this->getEndDateTimeTimestamp();
        return $ts > 0 && time() >= $ts;
    }
}