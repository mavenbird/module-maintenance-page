<?php
namespace Mavenbird\MaintenancePage\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\StoreManagerInterface;

class Preview extends Field
{
    private StoreManagerInterface $storeManager;

    public function __construct(Context $context, StoreManagerInterface $storeManager, array $data = [])
    {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    protected function _renderScopeLabel(AbstractElement $element): string { return ''; }
    protected function _renderLabelCell(AbstractElement $element): string { return '<td></td>'; }

    protected function _getElementHtml(AbstractElement $element): string
    {
        try {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        } catch (\Exception $e) {
            $baseUrl = '/';
        }

        $elementId = $element->getHtmlId();
        if (strpos($elementId, 'coming_soon') !== false) {
            $previewUrl = rtrim($baseUrl, '/') . '/maintenance/index/comingsoon?preview=1';
            $label      = __('Preview Coming Soon Page');
        } else {
            $previewUrl = rtrim($baseUrl, '/') . '/maintenance/index/index?preview=1';
            $label      = __('Preview Maintenance Page');
        }

        $html  = '<button type="button" onclick="window.open(\'' . $previewUrl . '\', \'_blank\');" '
            . 'style="background:#4f46e5;color:#fff;border:none;padding:10px 24px;border-radius:6px;'
            . 'font-size:14px;font-weight:600;cursor:pointer;" '
            . 'onmouseover="this.style.opacity=\'0.85\'" onmouseout="this.style.opacity=\'1\'">'
            . $label . '</button>';
        $html .= '<p style="margin-top:8px;font-size:12px;color:#666;">'
            . __('If uploading files, please save configuration before preview.') . '</p>';

        return $html;
    }
}