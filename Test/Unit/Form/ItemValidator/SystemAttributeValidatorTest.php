<?php declare(strict_types=1);

namespace Yireo\EavAttributeManager\Test\Unit\Form\ItemValidator;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use PHPUnit\Framework\TestCase;
use Yireo\EavAttributeManager\Form\ItemValidator\SystemAttributeValidator;

class SystemAttributeValidatorTest extends TestCase
{
    private const SYSTEM_ROW = [
        'attribute_id' => 73,
        'attribute_code' => 'sku',
        'entity_type_id' => 4,
        'backend_type' => 'static',
        'backend_table' => null,
        'is_user_defined' => 0,
    ];

    public function testNewItemIsValid(): void
    {
        $validator = $this->getValidator(false);

        $this->assertTrue($validator->validate(new DataObject(['attribute_code' => 'foo'])));
        $this->assertTrue($validator->validateDelete(new DataObject()));
    }

    public function testUserDefinedAttributeCanBeChangedAndDeleted(): void
    {
        $validator = $this->getValidator(array_merge(self::SYSTEM_ROW, ['is_user_defined' => 1]));
        $item = new DataObject(['attribute_id' => 73, 'attribute_code' => 'renamed']);

        $this->assertTrue($validator->validate($item));
        $this->assertTrue($validator->validateDelete($item));
    }

    public function testSystemAttributeCanNotBeDeleted(): void
    {
        $validator = $this->getValidator(self::SYSTEM_ROW);

        $this->assertCount(1, $validator->validateDelete(new DataObject(['attribute_id' => 73])));
    }

    public function testUnchangedSystemAttributeIsValid(): void
    {
        $validator = $this->getValidator(self::SYSTEM_ROW);
        $item = new DataObject(array_merge(self::SYSTEM_ROW, ['frontend_label' => 'SKU', 'is_required' => 1]));

        $this->assertTrue($validator->validate($item));
    }

    public function testLockedFieldsOfSystemAttributeCanNotBeChanged(): void
    {
        $validator = $this->getValidator(self::SYSTEM_ROW);
        $item = new DataObject(array_merge(self::SYSTEM_ROW, [
            'attribute_code' => 'sku2',
            'entity_type_id' => 1,
            'backend_type' => 'varchar',
            'backend_table' => 'admin_user',
            'is_user_defined' => 1,
        ]));

        $this->assertCount(count(SystemAttributeValidator::LOCKED_FIELDS), $validator->validate($item));
    }

    private function getValidator(array|false $row): SystemAttributeValidator
    {
        $select = $this->createStub(Select::class);
        $select->method('from')->willReturnSelf();
        $select->method('where')->willReturnSelf();

        $connection = $this->createStub(AdapterInterface::class);
        $connection->method('select')->willReturn($select);
        $connection->method('fetchRow')->willReturn($row);

        $resourceConnection = $this->createStub(ResourceConnection::class);
        $resourceConnection->method('getConnection')->willReturn($connection);
        $resourceConnection->method('getTableName')->willReturnArgument(0);

        return new SystemAttributeValidator($resourceConnection);
    }
}
