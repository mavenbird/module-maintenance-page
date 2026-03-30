<?php
namespace Mavenbird\MaintenancePage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BackgroundType implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'color', 'label' => __('Color')],
            ['value' => 'image', 'label' => __('Image')],
           
        ];
    }
}