<?php
namespace Mavenbird\MaintenancePage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class RedirectPage implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'maintenance',  'label' => __('Maintenance Page')],
            ['value' => 'coming_soon',  'label' => __('Coming Soon Page')],
        ];
    }
}