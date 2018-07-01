<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */
/**
 * @var $table \Database2Code\Struct\Table
 */

if(!function_exists('getterAndSetter__getColumnProperty')) {
    function getterAndSetter__getColumnProperty(\Database2Code\Struct\Column $column)
    {
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
        } else {
            $phpType = $column->getType()->getPHPTypeName();
        }
        return <<<END
    
    /** @var \${$column->getName()} $phpType */
    private \${$column->getName()};
END;
    }
}

if(!function_exists('getterAndSetter__getColumnMethods')) {
    function getterAndSetter__getColumnMethods(\Database2Code\Struct\Column $column)
    {
        $name = $column->getName();
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
            $returnTypeDef = '';
            $argumentTypeDef = '';
            $setterPHPDoc = <<<END
            
    /**
     * @param $phpType \${$name} 
     */
END;
            $getterPHPDoc = <<<END
            
    /**
     * @return $phpType
     */
END;

        } else {
            $phpType = $column->getType()->getPHPTypeName();
            $returnTypeDef = ' : '.$phpType;
            $argumentTypeDef = $phpType.' ';
            $getterPHPDoc = $setterPHPDoc = '';
        }
        $upperCaseName = strtoupper($name[0]) . substr($name, 1);
        return <<<END
    $getterPHPDoc
    public function get{$upperCaseName}()$returnTypeDef
    {
        return \$this->$name;
    }
    $setterPHPDoc
    public function set{$upperCaseName}($argumentTypeDef\$$name)
    {
        \$this->$name = \$$name;
    }
END;
    }
}

$properties = '';
foreach ($table->getColumns() as $column) {
    $properties .= getterAndSetter__getColumnProperty($column).PHP_EOL;
}

$methods = '';
foreach ($table->getColumns() as $column) {
    $methods .= getterAndSetter__getColumnMethods($column).PHP_EOL;
}

return <<<END
<?php

class {$table->getName()} {
$properties$methods
}
END;


