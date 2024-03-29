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

if(!function_exists('PHPFile__constants__getColumnConstant')) {
    function PHPFile__constants__getColumnConstant(\Database2Code\Struct\Column $column)
    {
        $constantName = $column->getName()==='from' ? '_'.$column->getName() : $column->getName();
        return <<<END

    const {$constantName} = '{$column->getName()}';
END;
    }
}

$properties = '';
foreach ($table->getColumns() as $column) {
    $properties .= PHPFile__constants__getColumnConstant($column).PHP_EOL;
}

if($config->hasNamespace()) {
    $namespace = "\r\nnamespace {$config->getNamespace()};\r\n";
} else {
    $namespace = '';
}

return <<<END
<?php
$namespace
class {$table->getName()} {

    const from = '{$table->getName()}';
$properties
}
END;

