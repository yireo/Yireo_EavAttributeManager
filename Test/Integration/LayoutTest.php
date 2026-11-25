<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Test\Integration;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\Action\SortAction;
use Loki\AdminComponents\Grid\StateManager;
use Loki\Components\Component\ComponentRegistry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;
use Yireo\EavAttributeManager\Form\ItemValidator\EavAttributeValidator;
use Yireo\EavAttributeManager\Form\ItemValidator\SystemAttributeValidator;

#[AppArea('adminhtml')]
class LayoutTest extends TestCase
{
    private const ACL_ARGUMENTS = [
        'acl_resource' => 'Yireo_EavAttributeManager::grid',
        'save_acl_resource' => 'Yireo_EavAttributeManager::save',
        'delete_acl_resource' => 'Yireo_EavAttributeManager::delete',
    ];

    public function testGridBlockHasAclResources(): void
    {
        $block = $this->getBlock('yireo_eav_attribute_manager_grid', 'yireo.eav-attribute-manager.grid');

        foreach (self::ACL_ARGUMENTS as $argument => $aclResource) {
            $this->assertSame($aclResource, $block->getData($argument), $argument);
        }
    }

    public function testFormBlockHasAclResourcesAndValidators(): void
    {
        $block = $this->getBlock('yireo_eav_attribute_manager_form', 'yireo.eav-attribute-manager.form');

        foreach (self::ACL_ARGUMENTS as $argument => $aclResource) {
            $this->assertSame($aclResource, $block->getData($argument), $argument);
        }

        $itemValidators = $block->getData('item_validators');
        $this->assertInstanceOf(EavAttributeValidator::class, $itemValidators['eav_attribute_validator']);
        $this->assertInstanceOf(SystemAttributeValidator::class, $itemValidators['system_attribute_validator']);

        $itemDeleteValidators = $block->getData('item_delete_validators');
        $this->assertInstanceOf(SystemAttributeValidator::class, $itemDeleteValidators['system_attribute_validator']);
    }

    public function testWriteActionsRequireModuleAclResources(): void
    {
        $block = $this->getBlock('yireo_eav_attribute_manager_form', 'yireo.eav-attribute-manager.form');

        $checkedResources = [];
        $authorization = $this->createMock(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturnCallback(
            function (string $resource) use (&$checkedResources) {
                $checkedResources[] = $resource;
                return false;
            }
        );

        $actionAuthorization = new ActionAuthorization($authorization);
        foreach (['save', 'duplicate', 'inline_edit', 'delete'] as $action) {
            $this->assertFalse($actionAuthorization->isAllowed($block, $action), $action);
        }

        $this->assertSame([
            'Yireo_EavAttributeManager::save',
            'Yireo_EavAttributeManager::save',
            'Yireo_EavAttributeManager::save',
            'Yireo_EavAttributeManager::delete',
        ], $checkedResources);
    }

    public function testGridRejectsSqlInSortField(): void
    {
        $block = $this->getBlock('yireo_eav_attribute_manager_grid', 'yireo.eav-attribute-manager.grid');
        $objectManager = ObjectManager::getInstance();
        $component = $objectManager->get(ComponentRegistry::class)->getComponentFromBlock($block);
        $repository = $component->getRepository();
        $this->assertInstanceOf(GridRepository::class, $repository);

        $state = $objectManager->get(StateManager::class)->get($repository->getNamespace());
        $sortAction = $objectManager->get(SortAction::class);

        $sortAction->execute($repository, ['sort' => ['sortBy' => 'attribute_code', 'sortDirection' => 'ASC']]);
        $this->assertSame('attribute_code', $state->getSortBy());

        $sortAction->execute($repository, [
            'sort' => ['sortBy' => 'attribute_id,(SELECT SLEEP(3))', 'sortDirection' => 'ASC'],
        ]);
        $this->assertSame('', $state->getSortBy());
    }

    private function getBlock(string $handle, string $blockName): AbstractBlock
    {
        $page = ObjectManager::getInstance()->get(PageFactory::class)->create();
        $page->addHandle($handle);

        $block = $page->getLayout()->getBlock($blockName);
        $this->assertInstanceOf(AbstractBlock::class, $block, 'Block "'.$blockName.'" not found');

        return $block;
    }
}
