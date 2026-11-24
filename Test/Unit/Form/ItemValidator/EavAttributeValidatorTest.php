<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Test\Unit\Form\ItemValidator;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend;
use Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Yireo\EavAttributeManager\Form\ItemValidator\EavAttributeValidator;

class EavAttributeValidatorTest extends TestCase
{
    public function testEmptyItemIsValid(): void
    {
        $this->assertTrue($this->getValidator()->validate(new DataObject()));
    }

    public function testMatchingModelClassesAreValid(): void
    {
        $item = new DataObject([
            'backend_model' => DefaultBackend::class,
            'frontend_model' => '\\'.DefaultFrontend::class,
            'source_model' => Boolean::class,
        ]);

        $this->assertTrue($this->getValidator()->validate($item));
    }

    public function testClassOfWrongTypeIsInvalid(): void
    {
        $item = new DataObject(['backend_model' => DataObject::class]);

        $this->assertCount(1, $this->getValidator()->validate($item));
    }

    public function testAttributeModelIsValidated(): void
    {
        $item = new DataObject(['attribute_model' => DefaultBackend::class]);

        $this->assertCount(1, $this->getValidator()->validate($item));
    }

    public function testMalformedClassNamesAreInvalid(): void
    {
        foreach (['Foo Bar', '../etc/passwd', 'Foo\\\\Bar', 'Foo;Bar'] as $className) {
            $item = new DataObject(['source_model' => $className]);
            $this->assertCount(1, $this->getValidator()->validate($item), $className);
        }
    }

    public function testGeneratedClassNamesAreRejectedBeforeAutoloading(): void
    {
        $className = 'Yireo\\EavAttributeManager\\Test\\DoesNotExistFactory';
        $item = new DataObject(['backend_model' => $className]);

        $this->assertCount(1, $this->getValidator()->validate($item));
        $this->assertFalse(class_exists($className, false));
    }

    public function testValueTableOfEntityTypeIsValidBackendTable(): void
    {
        $item = new DataObject([
            'entity_type_id' => 4,
            'backend_type' => 'varchar',
            'backend_table' => 'catalog_product_entity_varchar',
        ]);

        $this->assertTrue($this->getValidator()->validate($item));
    }

    public function testForeignTableIsInvalidBackendTable(): void
    {
        foreach (['admin_user', 'catalog_product_entity_int', 'catalog_product_entity_static'] as $table) {
            $item = new DataObject([
                'entity_type_id' => 4,
                'backend_type' => 'varchar',
                'backend_table' => $table,
            ]);

            $this->assertCount(1, $this->getValidator()->validate($item), $table);
        }
    }

    public function testUnchangedBackendTableIsValid(): void
    {
        $item = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->onlyMethods([])->getMock();
        $item->setData(['entity_type_id' => 4, 'backend_type' => 'varchar', 'backend_table' => 'custom_table']);
        $item->setOrigData('backend_table', 'custom_table');

        $this->assertTrue($this->getValidator()->validate($item));

        $item->setData('backend_table', 'other_table');
        $this->assertCount(1, $this->getValidator()->validate($item));
    }

    private function getValidator(): EavAttributeValidator
    {
        $entityType = $this->createStub(Type::class);
        $entityType->method('getValueTablePrefix')->willReturn('catalog_product_entity');

        $eavConfig = $this->createStub(EavConfig::class);
        $eavConfig->method('getEntityType')->willReturn($entityType);

        return new EavAttributeValidator($eavConfig);
    }
}
