<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\ViewModel\Options;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class BackendTypeOptions implements ArgumentInterface, OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $backendTypes = [
            'static',
            'varchar',
            'int',
            'text',
            'datetime',
            'decimal',
        ];

        $options = [];
        foreach ($backendTypes as $backendType) {
            $options[] = [
                'label' => __($backendType),
                'value' => $backendType,
            ];
        }

        return $options;
    }
}
