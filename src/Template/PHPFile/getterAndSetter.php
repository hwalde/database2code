<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

/** @var $table \Database2Code\Struct\Table */
/** @var $config \Database2Code\Output\OutputConfig */

if(!function_exists('PHPFile__getterAndSetter__getColumnProperty')) {
    function PHPFile__getterAndSetter__getColumnProperty(\Database2Code\Struct\Column $column)
    {
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
        } else {
            $phpType = $column->getType()->getPHPTypeName();
        }
        if($column->isNullable()) {
            $phpType .= '|null';
        }
        return <<<END
    
    /** @var \${$column->getName()} $phpType */
    private \${$column->getName()};
END;
    }
}

if(!function_exists('PHPFile__getterAndSetter__getColumnMethods')) {
    function PHPFile__getterAndSetter__getColumnMethods(\Database2Code\Struct\Column $column, \Database2Code\Output\OutputConfig $config)
    {
        $name = $column->getName();
        if($column->getType() instanceof \Database2Code\Struct\ColumnType\UnknownColumnType) {
            $phpType = 'mixed';
            if($column->isNullable()) {
                $phpType .= '|null';
            }
            $returnTypeDef = '';
            $argumentTypeDef = '';
        } else {
            $phpType = $column->getType()->getPHPTypeName();
            $argumentTypeDef = $phpType.' ';
            if($column->isNullable()) {
                if(version_compare($config->getPhpVersion(), '7.1', '>=')) {
                    $returnTypeDef = ' : ?'.$phpType;
                } else {
                    $returnTypeDef = '';
                }
                $phpType .= '|null';
            } else {
                $returnTypeDef = ' : '.$phpType;
            }
        }
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
    $properties .= PHPFile__getterAndSetter__getColumnProperty($column).PHP_EOL;
}

$methods = '';
foreach ($table->getColumns() as $column) {
    $methods .= PHPFile__getterAndSetter__getColumnMethods($column, $config).PHP_EOL;
}

if($config->hasNamespace()) {
    $namespace = "\nnamespace {$config->getNamespace()};\n";
} else {
    $namespace = '';
}

return <<<END
<?php
$namespace
class {$table->getName()} {
$properties$methods
}
END;


