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

    /** @var $constantsGenerator PHPFileGenerator */
    private $constantsGenerator;

    protected function setUp()
    {
        $this->getterAndSetterGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/getterAndSetter.php');
        $this->staticPropertiesGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/staticProperties.php');
        $this->constantsGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/constants.php');
    }

    public function testGetterSetterCodeGeneration()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $generatedCode = $this->getterAndSetterGenerator->generateFromTable($table, $config);

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

    public function testGetterSetterCodeGenerationWithNamespace()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $config->setNamespace('Hello\\World');
        $generatedCode = $this->getterAndSetterGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getExpectedGetterSetterSourcecodeWithNamespace(), $generatedCode);
    }

    private function getExpectedGetterSetterSourcecodeWithNamespace() {
        return <<<END
<?php

namespace Hello\\World;

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

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $generatedCode = $this->staticPropertiesGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getExpectedStaticPropertiesSourcecode(), $generatedCode);
    }

    private function getExpectedStaticPropertiesSourcecode() {
        return <<<END
<?php

class test_name {

    public static \$from = 'test_name';

    public static \$test = 'test';

    public static \$test2 = 'test2';

}
END;
    }

    public function testStaticPropertiesCodeGenerationWithNamespace()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $config->setNamespace('Hello\\World');
        $generatedCode = $this->staticPropertiesGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getExpectedStaticPropertiesSourcecodeWithNamespace(), $generatedCode);
    }

    private function getExpectedStaticPropertiesSourcecodeWithNamespace() {
        return <<<END
<?php

namespace Hello\World;

class test_name {

    public static \$from = 'test_name';

    public static \$test = 'test';

    public static \$test2 = 'test2';

}
END;
    }


    public function testConstantsCodeGeneration()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $generatedCode = $this->constantsGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getConstantsPropertiesSourcecode(), $generatedCode);
    }

    private function getConstantsPropertiesSourcecode() {
        return <<<END
<?php

class test_name {

    const from = 'test_name';

    const test = 'test';

    const test2 = 'test2';

}
END;
    }

    public function testConstantsCodeGenerationWithNamespace()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\PHPFile\PHPFileOutputConfig();
        $config->setNamespace('Hello\\World');
        $generatedCode = $this->constantsGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getExpectedConstantsSourcecodeWithNamespace(), $generatedCode);
    }

    private function getExpectedConstantsSourcecodeWithNamespace() {
        return <<<END
<?php

namespace Hello\World;

class test_name {

    const from = 'test_name';

    const test = 'test';

    const test2 = 'test2';

}
END;
    }

}