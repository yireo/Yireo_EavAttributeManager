<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Test\Integration;

use Magento\Framework\Acl\AclResource\ProviderInterface;
use Magento\Framework\App\ObjectManager;
use PHPUnit\Framework\TestCase;
use Yireo\EavAttributeManager\Controller\Adminhtml\Index\Form;
use Yireo\EavAttributeManager\Controller\Adminhtml\Index\Grid;

class AclTest extends TestCase
{
    public function testAclResourcesAreDeclaredBelowStoresAttributes(): void
    {
        $resources = ObjectManager::getInstance()->get(ProviderInterface::class)->getAclResources();
        $path = $this->findResourcePath($resources, 'Yireo_EavAttributeManager::index');

        $this->assertSame(
            ['Magento_Backend::admin', 'Magento_Backend::stores', 'Magento_Backend::stores_attributes'],
            $path
        );

        foreach (['grid', 'save', 'delete'] as $child) {
            $this->assertSame(
                array_merge($path, ['Yireo_EavAttributeManager::index']),
                $this->findResourcePath($resources, 'Yireo_EavAttributeManager::'.$child),
                $child
            );
        }
    }

    public function testControllersUseDeclaredAclResources(): void
    {
        $resources = ObjectManager::getInstance()->get(ProviderInterface::class)->getAclResources();

        $this->assertSame('Yireo_EavAttributeManager::grid', Grid::ADMIN_RESOURCE);
        $this->assertSame('Yireo_EavAttributeManager::save', Form::ADMIN_RESOURCE);
        $this->assertNotNull($this->findResourcePath($resources, Grid::ADMIN_RESOURCE));
        $this->assertNotNull($this->findResourcePath($resources, Form::ADMIN_RESOURCE));
    }

    private function findResourcePath(array $resources, string $id, array $parents = []): ?array
    {
        foreach ($resources as $resource) {
            if ($resource['id'] === $id) {
                return $parents;
            }

            $path = $this->findResourcePath($resource['children'] ?? [], $id, array_merge($parents, [$resource['id']]));
            if ($path !== null) {
                return $path;
            }
        }

        return null;
    }
}
