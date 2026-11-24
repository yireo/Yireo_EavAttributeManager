<?php
declare(strict_types=1);

namespace Yireo\EavAttributeManager\Form\ItemValidator;

use Loki\AdminComponents\Form\Item\ItemDeleteValidatorInterface;
use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;

class SystemAttributeValidator implements ItemValidatorInterface, ItemDeleteValidatorInterface
{
    public const LOCKED_FIELDS = [
        'attribute_code',
        'entity_type_id',
        'backend_type',
        'backend_table',
        'is_user_defined',
    ];

    public function __construct(
        private ResourceConnection $resourceConnection,
    ) {
    }

    public function validate(DataObject $item): true|array
    {
        $originalRow = $this->getOriginalRow($item);
        if (false === $this->isSystemAttribute($originalRow)) {
            return true;
        }

        $errors = [];
        foreach (self::LOCKED_FIELDS as $field) {
            if (false === $item->hasData($field)) {
                continue;
            }

            if ((string)$item->getData($field) !== (string)($originalRow[$field] ?? '')) {
                $errors[] = __(
                    'Field "%1" of system attribute "%2" can not be changed',
                    $field,
                    $originalRow['attribute_code']
                );
            }
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    public function validateDelete(DataObject $item): true|array
    {
        $originalRow = $this->getOriginalRow($item);
        if (false === $this->isSystemAttribute($originalRow)) {
            return true;
        }

        return [__('System attribute "%1" can not be deleted', $originalRow['attribute_code'])];
    }

    private function isSystemAttribute(array $originalRow): bool
    {
        return !empty($originalRow) && (int)$originalRow['is_user_defined'] === 0;
    }

    private function getOriginalRow(DataObject $item): array
    {
        $attributeId = (int)$item->getData('attribute_id');
        if ($attributeId < 1) {
            return [];
        }

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('eav_attribute'))
            ->where('attribute_id = ?', $attributeId);

        $row = $connection->fetchRow($select);

        return is_array($row) ? $row : [];
    }
}
