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

    protected function setUp(): void
    {
        $this->getterAndSetterGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/getterAndSetter.php');
        $this->staticPropertiesGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/staticProperties.php');
        $this->constantsGenerator = new PHPFileGenerator(__DIR__.'/../../../src/Template/PHPFile/constants.php');
    }

    public function testGetterSetterCodeGenerationAndPHPVersion7Point0()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\OutputConfig();
        $config->setPhpVersion('7.0');
        $config->setNamespace("\\test\\test2");
        $generatedCode = $this->getterAndSetterGenerator->generateFromTable($table, $config);

        $this->assertEquals($this->getExpectedGetterSetterSourcecode(), $generatedCode);
    }

    private function getValidTwoColumnTableStruct(): \Database2Code\Struct\Table
    {
        $table = new \Database2Code\Struct\Table('test_name');
        $table->setColumns([
            new \Database2Code\Struct\Column('test', new \Database2Code\Struct\ColumnType\StringColumnType('varchar(60)'), false),
            new \Database2Code\Struct\Column('test2', new \Database2Code\Struct\ColumnType\IntegerColumnType('varchar(60)', 10), true)
        ]);
        return $table;
    }

    private function getExpectedGetterSetterSourcecode() {
        return <<<END
<?php

namespace \\test\\test2;

class test_name {
    
    /** @var \$test string */
    private \$test;
    
    /** @var \$test2 int|null */
    private \$test2;

    /**
     * @return string
     */
    public function getTest() : string
    {
        return \$this->test;
    }

    /**
     * @param string \$test
     */
    public function setTest(string \$test)
    {
        \$this->test = \$test;
    }

    /**
     * @return int|null
     */
    public function getTest2()
    {
        return \$this->test2;
    }

    /**
     * @param int|null \$test2
     */
    public function setTest2(\$test2)
    {
        \$this->test2 = \$test2;
    }

}
END;

    }

    public function testGetterSetterCodeGenerationWithNamespaceAndPHP7Point1()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\OutputConfig();
        $config->setNamespace('Hello\\World');
        $config->setPhpVersion('7.1');
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
    
    /** @var \$test2 int|null */
    private \$test2;

    /**
     * @return string
     */
    public function getTest() : string
    {
        return \$this->test;
    }

    /**
     * @param string \$test
     */
    public function setTest(string \$test)
    {
        \$this->test = \$test;
    }

    /**
     * @return int|null
     */
    public function getTest2() : ?int
    {
        return \$this->test2;
    }

    /**
     * @param int|null \$test2
     */
    public function setTest2(?int \$test2)
    {
        \$this->test2 = \$test2;
    }

}
END;
    }

    public function testStaticPropertiesCodeGeneration()
    {
        $table = $this->getValidTwoColumnTableStruct();

        $config = new \Database2Code\Output\OutputConfig();
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

        $config = new \Database2Code\Output\OutputConfig();
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

        $config = new \Database2Code\Output\OutputConfig();
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

        $config = new \Database2Code\Output\OutputConfig();
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