<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Output;


use Database2Code\Output\OutputConfig;
use Database2Code\Struct\Table;

interface Output
{
    public function saveTable(Table $table, string $targetFolderPath);
}