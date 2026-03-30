<?php
namespace Mavenbird\MaintenancePage\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class HttpResponse implements ArrayInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '200', 'label' => __('200 OK')],
            ['value' => '503', 'label' => __('503 Service Unavailable')],
        ];
    }
}