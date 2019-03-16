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
        return <<<END

    const {$column->getName()} = '{$column->getName()}';
END;
    }
}

$properties = '';
foreach ($table->getColumns() as $column) {
    $properties .= PHPFile__constants__getColumnConstant($column).PHP_EOL;
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

    const from = '{$table->getName()}';
$properties
}
END;

