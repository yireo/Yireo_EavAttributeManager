<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Test\Integration\Form\ItemValidator;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Yireo\EavAttributeManager\Form\ItemValidator\SystemAttributeValidator;

class SystemAttributeValidatorTest extends TestCase
{
    public function testSkuCanNotBeDeletedOrRenamed(): void
    {
        $attribute = ObjectManager::getInstance()->get(EavConfig::class)->getAttribute('catalog_product', 'sku');
        $this->assertSame(0, (int)$attribute->getIsUserDefined());

        $validator = ObjectManager::getInstance()->get(SystemAttributeValidator::class);
        $item = new DataObject($attribute->getData());

        $this->assertIsArray($validator->validateDelete($item));
        $this->assertTrue($validator->validate($item));

        $item->setData('attribute_code', 'sku_renamed');
        $item->setData('is_user_defined', 1);
        $this->assertCount(2, $validator->validate($item));
    }

    public function testUnknownAttributeIsNotProtected(): void
    {
        $validator = ObjectManager::getInstance()->get(SystemAttributeValidator::class);
        $item = new DataObject(['attribute_id' => 999999999]);

        $this->assertTrue($validator->validateDelete($item));
        $this->assertTrue($validator->validate($item));
    }
}
