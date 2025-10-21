<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Eav\Model\Entity\TypeFactory;

class EntityType implements ArgumentInterface
{
    public function __construct(
        private TypeFactory $entityTypeFactory,
    ) {
    }

    public function getCodeById(int $entityTypeId): string
    {
        // @phpstan-ignore-next-line
        $entityType = $this->entityTypeFactory->create()->load($entityTypeId);
        return $entityType->getEntityTypeCode();
    }
}
