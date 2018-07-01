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


if(!function_exists('staticProperties__getColumnMethods')) {
    function staticProperties__getColumnMethods(\Database2Code\Struct\Column $column)
    {
        return <<<END
    
    public static \${$column->getName()} = '{$column->getName()}';
END;
    }
}

$properties = '';
foreach ($table->getColumns() as $column) {
    $properties .= staticProperties__getColumnMethods($column).PHP_EOL;
}

return <<<END
<?php

class {$table->getName()} {
$properties
}
END;


