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
use Database2Code\Output\Output;
use Database2Code\Struct\Table;

class PHPFileOutput implements Output
{
    /** @var $simplePhpGenerator PHPFileGenerator */
    protected $simplePhpGenerator;

    public function __construct(OutputConfig $outputFileConfig)
    {
        if($outputFileConfig->hasCustomTemplatePath()) {
            $templateFilepath = $outputFileConfig->getCustomTemplatePath();
        } else {
            $templateFilepath = __DIR__.'/../../Template/PHPFile/getterAndSetter.php';
        }
        $this->simplePhpGenerator = new PHPFileGenerator($templateFilepath);
    }

    public function saveTable(Table $table, string $targetDirectoryPath) {
        $fileContents = $this->simplePhpGenerator->generateFromTable($table);
        $filePath = $this->generateFilepath($targetDirectoryPath, $table->getName());

        file_put_contents($filePath, $fileContents);
    }

    protected function generateFilepath(string $targetFolderPath, string $tablename) {
        return $targetFolderPath.DIRECTORY_SEPARATOR.$tablename.'.php';
    }
}