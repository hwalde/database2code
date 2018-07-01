<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Service;


use Database2Code\Input\MySQL\MySQLInputConfig;
use Database2Code\Input\InputConfig;
use Database2Code\Input\MySQL\MySQLInput;
use Database2Code\Input\Input;
use Database2Code\Input\MySQL\MySQLTableHydrator;
use Database2Code\Output\Output;

class ConvertService
{

    /** @var $inputMap array */
    private $inputMap;

    /** @var $output \Database2Code\Output\Output */
    private $output;

    public function __construct(Output $output)
    {
        $this->inputMap = [];
        $this->output = $output;
    }

    public function convertDatabase(InputConfig $inputConfig, string $database, string $targetFolderPath) {
        $input = $this->getInput($inputConfig, $database);

        foreach ($input->listTables() as $tablename) {
            $this->convertTable($inputConfig, $database, $tablename, $targetFolderPath);
        }
    }

    private function getInput(InputConfig $dbConfig, string $database) : Input
    {
        $id = spl_object_hash($dbConfig).'|||'.$database;
        if(!isset($this->inputMap[$id])) {
            $this->inputMap[$id] = $this->generateInputInstance($dbConfig, $database);
        }
        return $this->inputMap[$id];
    }

    private function generateInputInstance(InputConfig $inputConfig, string $database) : Input
    {
        if ($inputConfig instanceof MySQLInputConfig) {
            return new MySQLInput($inputConfig, $database, new MySQLTableHydrator());
        }
        throw new \Error('Unknown DBConfigInterface "' . get_class($inputConfig) . '"!');
    }

    public function convertTable(InputConfig $inputConfig, string $database, string $tableName, string $targetFolderPath) {
        $input = $this->getInput($inputConfig, $database);

        $table = $input->getTableStruct($tableName);

        $this->validateTargetFolder($targetFolderPath);

        $this->output->saveTable($table, $targetFolderPath);
    }

    private function validateTargetFolder(string $targetFolderPath)
    {
        if (!is_dir($targetFolderPath)) {
            throw new \Error('Target folder "' . $targetFolderPath . '" not found!');
        }
        if (!is_writable($targetFolderPath)) {
            throw new \Error('Target folder "' . $targetFolderPath . '" is not writeable!');
        }
    }

}