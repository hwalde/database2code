<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

use Database2Code\Output\PHPFile\PHPFileGenerator;
use PHPUnit\Framework\TestCase;

class PHPGeneratorTest extends TestCase
{
    /** @var $simplePhpGenerator PHPFileGenerator */
    private $getterAndSetterGenerator;

    /** @var $simplePhpGenerator PHPFileGenerator */
    private $staticPropertiesGenerator;

    protected function setUp()
    {
        $this->getterAndSetterGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/getterAndSetter.php');
        $this->staticPropertiesGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/staticProperties.php');
    }

    public function testGetterSetterCodeGeneration()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $generatedCode = $this->getterAndSetterGenerator->generateFromTable($table);

        $this->assertEquals($this->getExpectedGetterSetterSourcecode(), $generatedCode);
    }

    private function getValidTwoColumnTableStruct(): \Database2Code\Struct\Table
    {
        $table = new \Database2Code\Struct\Table('test_name');
        $table->setColumns([
            new \Database2Code\Struct\Column('test', new \Database2Code\Struct\ColumnType\StringColumnType('varchar(60)')),
            new \Database2Code\Struct\Column('test2', new \Database2Code\Struct\ColumnType\IntegerColumnType('varchar(60)', 10))
        ]);
        return $table;
    }

    private function getExpectedGetterSetterSourcecode() {
        return <<<END
<?php

class test_name {
    
    /** @var \$test string */
    private \$test;
    
    /** @var \$test2 int */
    private \$test2;
    
    public function getTest() : string
    {
        return \$this->test;
    }
    
    public function setTest(string \$test)
    {
        \$this->test = \$test;
    }
    
    public function getTest2() : int
    {
        return \$this->test2;
    }
    
    public function setTest2(int \$test2)
    {
        \$this->test2 = \$test2;
    }

}
END;
    }

    public function testStaticPropertiesCodeGeneration()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $generatedCode = $this->staticPropertiesGenerator->generateFromTable($table);

        $this->assertEquals($this->getExpectedStaticPropertiesSourcecode(), $generatedCode);
    }

    private function getExpectedStaticPropertiesSourcecode() {
        return <<<END
<?php

class test_name {
    
    public static \$test = 'test';
    
    public static \$test2 = 'test2';

}
END;
    }

}