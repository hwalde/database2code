<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

class MySQLTableHydratorTest extends \PHPUnit\Framework\TestCase
{

    public function testHydrateWithValidData()
    {
        $hydrator = new \Database2Code\Input\MySQL\MySQLTableHydrator();
        $table = $hydrator->hydrate('testtable', [
            ['Field' => 'id', 'Type'=>'int(8)', 'Null'=>'NO'],
            ['Field' => 'supplierID', 'Type'=>'int(11) unsigned', 'Null'=>'NO'],
            ['Field' => 'name', 'Type'=>'varchar(100)', 'Null'=>'NO'],
            ['Field' => 'description', 'Type'=>'mediumtext', 'Null'=>'YES'],
        ]);
        $this->assertInstanceOf(\Database2Code\Struct\Table::class, $table);
        $this->assertEquals('testtable', $table->getName());
        $this->assertIsArray($table->getColumns());
        $this->assertEquals(4, count($table->getColumns()));

        /** @var $column1 \Database2Code\Struct\Column */
        $column1 = $table->getColumns()[0];
        $this->assertInstanceOf(\Database2Code\Struct\Column::class, $column1);
        $this->assertEquals('id', $column1->getName());
        $this->assertInstanceOf(\Database2Code\Struct\ColumnType\IntegerColumnType::class, $column1->getType());
        $this->assertFalse($column1->isNullable());

        /** @var $column1 \Database2Code\Struct\Column */
        $column2 = $table->getColumns()[1];
        $this->assertInstanceOf(\Database2Code\Struct\Column::class, $column2);
        $this->assertEquals('supplierID', $column2->getName());
        $this->assertInstanceOf(\Database2Code\Struct\ColumnType\IntegerColumnType::class, $column2->getType());
        $this->assertFalse($column2->isNullable());

        /** @var $column1 \Database2Code\Struct\Column */
        $column3 = $table->getColumns()[2];
        $this->assertInstanceOf(\Database2Code\Struct\Column::class, $column3);
        $this->assertEquals('name', $column3->getName());
        $this->assertInstanceOf(\Database2Code\Struct\ColumnType\StringColumnType::class, $column3->getType());
        $this->assertFalse($column3->isNullable());

        /** @var $column1 \Database2Code\Struct\Column */
        $column4 = $table->getColumns()[3];
        $this->assertInstanceOf(\Database2Code\Struct\Column::class, $column4);
        $this->assertEquals('description', $column4->getName());
        $this->assertInstanceOf(\Database2Code\Struct\ColumnType\StringColumnType::class, $column4->getType());
        $this->assertTrue($column4->isNullable());
    }

}
