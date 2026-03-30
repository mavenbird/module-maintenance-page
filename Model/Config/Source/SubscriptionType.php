<?php
namespace Mavenbird\MaintenancePage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SubscriptionType implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'newsletter', 'label' => __('Newsletter Subscription')],
            ['value' => 'register',   'label' => __('Register Account')],
        ];
    }
}