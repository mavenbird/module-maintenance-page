<?php
namespace Mavenbird\MaintenancePage\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DateTimePicker extends Field
{
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $elementId = $element->getHtmlId();
        $value     = $element->getValue() ?: '';

        // Parse stored value "MM/DD/YYYY HH:MM" into date + time parts for pre-filling
        $dateVal = '';
        $timeVal = '';
        if ($value) {
            // Match MM/DD/YYYY HH:MM  (time may or may not include seconds)
            if (preg_match('#^(\d{2})/(\d{2})/(\d{4})\s+(\d{2}:\d{2})#', $value, $m)) {
                $dateVal = $m[3] . '-' . $m[1] . '-' . $m[2]; // YYYY-MM-DD  (HTML date input format)
                $timeVal = $m[4];                              // HH:MM
            }
        }

        $safeValue    = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $safeDateVal  = htmlspecialchars($dateVal, ENT_QUOTES, 'UTF-8');
        $safeTimeVal  = htmlspecialchars($timeVal, ENT_QUOTES, 'UTF-8');
        $jsValue      = addslashes($value);

        $html  = '<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">';

        // Date input — pre-filled from PHP so it survives hard-refresh without JS
        $html .= '<input type="date"'
            . ' id="' . $elementId . '_date"'
            . ' value="' . $safeDateVal . '"'
            . ' style="padding:8px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;"'
            . ' onchange="mvUpdateDateTime(\'' . $elementId . '\')" />';

        // Time input — pre-filled from PHP
        $html .= '<input type="time"'
            . ' id="' . $elementId . '_time"'
            . ' value="' . $safeTimeVal . '"'
            . ' style="padding:8px 12px;border:1px solid #ccc;border-radius:4px;font-size:14px;"'
            . ' onchange="mvUpdateDateTime(\'' . $elementId . '\')" />';

        // Hidden field that holds the real saved value sent to Magento
        $html .= '<input type="hidden"'
            . ' id="' . $elementId . '"'
            . ' name="' . $element->getName() . '"'
            . ' value="' . $safeValue . '" />';

        // Human-readable display
        $html .= '<span id="' . $elementId . '_display"'
            . ' style="font-size:13px;color:#666;">'
            . ($value
                ? 'Current: <b>' . $safeValue . '</b>'
                : 'Not set')
            . '</span>';

        $html .= '</div>';

        // Inline JS — only the update function; pre-fill is handled by PHP value="" above
        $html .= '<script>
(function () {
    // Guard: define mvUpdateDateTime only once across multiple fields on the page
    if (typeof window.mvUpdateDateTime === "undefined") {
        window.mvUpdateDateTime = function (id) {
            var dateEl = document.getElementById(id + "_date");
            var timeEl = document.getElementById(id + "_time");
            var hidEl  = document.getElementById(id);
            var dispEl = document.getElementById(id + "_display");

            if (!dateEl || !hidEl) return;

            var d = dateEl.value;          // YYYY-MM-DD
            var t = timeEl ? timeEl.value : "";
            if (!t) t = "00:00";

            if (d) {
                var parts = d.split("-");  // [YYYY, MM, DD]
                // Store as MM/DD/YYYY HH:MM  (matches what the model/config expects)
                var formatted = parts[1] + "/" + parts[2] + "/" + parts[0] + " " + t;
                hidEl.value = formatted;
                if (dispEl) {
                    dispEl.innerHTML = "Set to: <b>" + formatted + "</b>";
                }
            }
        };
    }
})();
</script>';

        return $html;
    }
}