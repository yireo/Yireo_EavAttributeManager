<?php
declare(strict_types=1);

namespace Yireo\EavAttributeManager\Form\ItemValidator;

use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Magento\Framework\DataObject;

class EavAttributeValidator implements ItemValidatorInterface
{
    public function validate(DataObject $item): true|array
    {
        $errors = [];
        if (false === $this->isValidClassName($item->getFrontendModel())) {
            $errors[] = __('Frontend model "%1" is invalid', $item->getFrontendModel());
        }

        if (false === $this->isValidClassName($item->getSourceModel())) {
            $errors[] = __('Source model "%1" is invalid', $item->getSourceModel());
        }

        if (false === $this->isValidClassName($item->getBackendModel())) {
            $errors[] = __('Backend model "%1" is invalid', $item->getBackendModel());
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    private function isValidClassName(?string $className = null): bool
    {
        $className = (string)$className;
        if (empty($className)) {
            return true;
        }

        return class_exists($className);
    }
}
