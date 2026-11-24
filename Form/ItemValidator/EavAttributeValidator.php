<?php
declare(strict_types=1);

namespace Yireo\EavAttributeManager\Form\ItemValidator;

use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\BackendInterface;
use Magento\Eav\Model\Entity\Attribute\Frontend\FrontendInterface;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Phrase;

class EavAttributeValidator implements ItemValidatorInterface
{
    private const CLASS_FIELDS = [
        'frontend_model' => FrontendInterface::class,
        'source_model' => SourceInterface::class,
        'backend_model' => BackendInterface::class,
        'attribute_model' => AbstractAttribute::class,
    ];

    private const CLASS_NAME_PATTERN = '/^\\\\?[A-Za-z_][A-Za-z0-9_]*(\\\\[A-Za-z_][A-Za-z0-9_]*)*$/';

    private const GENERATED_CLASS_PATTERN = '/(Factory|Proxy|Interceptor)$/';

    public function __construct(
        private EavConfig $eavConfig,
    ) {
    }

    public function validate(DataObject $item): true|array
    {
        $errors = [];
        foreach (self::CLASS_FIELDS as $field => $requiredType) {
            $className = (string)$item->getData($field);
            if ($className === '' || $this->isValidClassName($className, $requiredType)) {
                continue;
            }

            $errors[] = __('Value of "%1" is not a valid class implementing %2', $field, $requiredType);
        }

        $backendTableError = $this->getBackendTableError($item);
        if ($backendTableError instanceof Phrase) {
            $errors[] = $backendTableError;
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    private function isValidClassName(string $className, string $requiredType): bool
    {
        if (!preg_match(self::CLASS_NAME_PATTERN, $className)) {
            return false;
        }

        if (preg_match(self::GENERATED_CLASS_PATTERN, $className)) {
            return false;
        }

        if (!class_exists($className)) {
            return false;
        }

        return is_a($className, $requiredType, true);
    }

    private function getBackendTableError(DataObject $item): ?Phrase
    {
        $backendTable = (string)$item->getData('backend_table');
        $originalBackendTable = $item instanceof AbstractModel ? (string)$item->getOrigData('backend_table') : '';
        if ($backendTable === '' || $backendTable === $originalBackendTable) {
            return null;
        }

        $error = __('Value of "%1" is not a valid value table for this entity type', 'backend_table');

        try {
            $entityType = $this->eavConfig->getEntityType((int)$item->getData('entity_type_id'));
        } catch (LocalizedException $exception) {
            return $error;
        }

        $valueTablePrefix = (string)$entityType->getValueTablePrefix();
        $backendType = (string)$item->getData('backend_type');
        if ($valueTablePrefix === '' || $backendType === '' || $backendType === 'static') {
            return $error;
        }

        if ($backendTable !== $valueTablePrefix.'_'.$backendType) {
            return $error;
        }

        return null;
    }
}
