<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Input;


use Database2Code\Input\InputConfig;
use Database2Code\Struct\Table;

interface Input
{
    public function listTables() : array;

    public function getTableStruct(string $name) : Table;
}