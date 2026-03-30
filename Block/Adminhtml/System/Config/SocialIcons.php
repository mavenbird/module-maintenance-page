<?php
namespace Mavenbird\MaintenancePage\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class SocialIcons extends Field
{
    /**
     * Render social icons in admin config
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $icons = [
            'Facebook'  => 'https://cdn-icons-png.flaticon.com/512/124/124010.png',
            'Twitter'   => 'https://cdn-icons-png.flaticon.com/512/124/124021.png',
            'Instagram' => 'https://cdn-icons-png.flaticon.com/512/2111/2111463.png',
            'YouTube'   => 'https://cdn-icons-png.flaticon.com/512/1384/1384060.png',
            
        ];

        $html = '<div class="mavenbird-social-icons">';

        foreach ($icons as $name => $icon) {
            $html .= '<div class="icon-box">';
            $html .= '<img src="' . $this->escapeUrl($icon) . '" alt="' . $this->escapeHtml($name) . '" />';
            $html .= '<span>' . $this->escapeHtml($name) . '</span>';
            $html .= '</div>';
        }

        $html .= '</div>';

        // Add custom CSS
        $html .= '<style>
            .mavenbird-social-icons {
                display: flex;
                gap: 20px;
                margin-top: 10px;
            }
            .mavenbird-social-icons .icon-box {
                text-align: center;
                cursor: pointer;
            }
            .mavenbird-social-icons img {
                width: 32px;
                height: 32px;
                transition: 0.3s;
            }
            .mavenbird-social-icons img:hover {
                transform: scale(1.2);
            }
            .mavenbird-social-icons span {
                display: block;
                font-size: 12px;
                margin-top: 5px;
                color: #555;
            }
        </style>';

        return $html;
    }
}