<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Output\PHPFile;


use Database2Code\Output\OutputConfig;
use Database2Code\Struct\Table;

class PHPFileGenerator
{
    /** @var $templateFilepath string */
    private $templateFilepath;

    public function __construct(string $templateFilepath)
    {
        $this->templateFilepath = $templateFilepath;
    }

    public function generateFromTable(Table $table, OutputConfig $config) : string
    {
        // $table and $config are used in:
        return include $this->templateFilepath;
    }
}