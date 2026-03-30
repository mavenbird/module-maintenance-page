<?php
namespace Mavenbird\MaintenancePage\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorPicker extends Field
{
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     */
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }

    /**
     * Render color picker field with JS color picker and clear option
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $elementId = $element->getHtmlId();
        $value     = $element->getValue(); // Allow empty

        $safeValue = $value ? htmlspecialchars($value) : '';

        $html = '<div style="display:flex;align-items:center;gap:10px;">';

        // Swatch preview
        $html .= '<div id="' . $elementId . '_swatch" 
            style="width:36px;height:36px;border-radius:6px;border:1px solid #ccc;
            background:' . ($safeValue ?: '#ffffff') . ';cursor:pointer;flex-shrink:0;"
            onclick="document.getElementById(\'' . $elementId . '_picker\').click();">
        </div>';

        // Hidden color picker
        $html .= '<input type="color"
            id="' . $elementId . '_picker"
            value="' . ($safeValue ?: '#000000') . '"
            style="opacity:0;width:0;height:0;position:absolute;"
            onchange="
                document.getElementById(\'' . $elementId . '\').value=this.value;
                document.getElementById(\'' . $elementId . '_text\').value=this.value;
                document.getElementById(\'' . $elementId . '_swatch\').style.background=this.value;
            " />';

        // Text input for hex value
        $html .= '<input type="text"
            id="' . $elementId . '_text"
            value="' . $safeValue . '"
            placeholder="#ffffff"
            maxlength="7"
            style="width:90px;padding:6px 10px;border:1px solid #ccc;border-radius:4px;font-family:monospace;"
            oninput="
                var v=this.value;
                if(v===\'\'){
                    document.getElementById(\'' . $elementId . '\').value=\'\';
                    document.getElementById(\'' . $elementId . '_swatch\').style.background=\'#ffffff\';
                    return;
                }
                if(/^#[0-9A-Fa-f]{6}$/.test(v)){
                    document.getElementById(\'' . $elementId . '\').value=v;
                    document.getElementById(\'' . $elementId . '_swatch\').style.background=v;
                    document.getElementById(\'' . $elementId . '_picker\').value=v;
                }
            " />';

        // Clear button
        $html .= '<button type="button"
            style="padding:6px 10px;border:1px solid #ccc;background:#f6f6f6;cursor:pointer;border-radius:4px;"
            onclick="
                document.getElementById(\'' . $elementId . '\').value=\'\';
                document.getElementById(\'' . $elementId . '_text\').value=\'\';
                document.getElementById(\'' . $elementId . '_swatch\').style.background=\'#ffffff\';
            ">
            Clear
        </button>';

        // Hidden actual form input
        $html .= '<input type="hidden"
            id="' . $elementId . '"
            name="' . $element->getName() . '"
            value="' . $safeValue . '" />';

        $html .= '</div>';

        return $html;
    }
}