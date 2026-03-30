<?php
namespace Mavenbird\MaintenancePage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ClockStyle implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'simple', 'label' => __('Simple')],
            ['value' => 'circle', 'label' => __('Circle')],
            ['value' => 'square', 'label' => __('Square')],
            ['value' => 'stack',  'label' => __('Stack')],
            ['value' => 'modern', 'label' => __('Modern')],
        ];
    }
}