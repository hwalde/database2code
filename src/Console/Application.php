<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */

namespace Database2Code\Console;


use Database2Code\Input\MySQL\MySQLInputConfig;
use Database2Code\Input\InputConfig;
use Database2Code\Output\OutputConfig;
use Database2Code\Output\PHPFile\PHPFileOutput;
use Database2Code\Service\ConvertService;

class Application
{
    /** @var $getOpt \GetOpt\GetOpt */
    private $getOpt;

    public function __construct()
    {
        $this->setupGetOpt();
    }

    private function setupGetOpt()
    {
        $this->getOpt = new \GetOpt\GetOpt([
            // DataBase Management System type
            \GetOpt\Option::create(null, 'dbms', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('Database Type (mysql)')
                ->setArgument(new \GetOpt\Argument('mysql', null, 'dbms')),
        ]);

        $this->addGetOptDBMSOptions();

        $this->getOpt->addOptions([
            \GetOpt\Option::create(null, 'customTemplate', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('Use a custom output-file template')
                ->setArgument(new \GetOpt\Argument(null, null, 'filePath')),

            \GetOpt\Option::create(null, 'customOutputFileGateway', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('Use can specify a custom output-file generator')
                ->setArgument(new \GetOpt\Argument(null, null, 'FQN')),

            \GetOpt\Option::create(null, 'xml-config-file', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('Read database config from an xml file')
                ->setArgument(new \GetOpt\Argument(null, null, 'filePath')),
        ]);

        $this->getOpt->addOperand(new \GetOpt\Operand('pathToOutputFolder', \GetOpt\Operand::REQUIRED));
        $this->getOpt->addOperand(new \GetOpt\Operand('database-name', \GetOpt\Operand::REQUIRED));
        $this->getOpt->addOperand(new \GetOpt\Operand('table', \GetOpt\Operand::OPTIONAL));

        $this->getOpt->process();
    }

    private function addGetOptDBMSOptions()
    {
        // MySQL Specific options
        $this->getOpt->addOptions([
            \GetOpt\Option::create('h', 'mysql-host', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Hostname'),
            \GetOpt\Option::create('u', 'mysql-user', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Username'),
            \GetOpt\Option::create('p', 'mysql-password', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Password'),
            \GetOpt\Option::create(null, 'mysql-port', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Port')
                ->setDefaultValue(3306)
                ->setValidation('is_numeric')
        ]);

        // Add your dbms options here..
    }

    public function run()
    {
        $outputGateway = $this->getOutputGateway();
        $service = new ConvertService($outputGateway);
        $dbConfig = $this->getDatabaseConfig();
        $databaseName = $this->getDatabaseName();
        $pathToOutputFolder = $this->getOpt->getOperand('pathToOutputFolder');
        $tableName = $this->getOpt->getOperand('table');

        if(is_null($tableName)) {
            echo 'Converting table "'.$databaseName.'.'.$tableName.'" to output-folder "'.$pathToOutputFolder.'"'.PHP_EOL;
            $service->convertDatabase($dbConfig, $databaseName, $pathToOutputFolder);
        } else {
            echo 'Converting database "'.$databaseName.'" to output-folder "'.$pathToOutputFolder.'"'.PHP_EOL;
            $service->convertTable($dbConfig, $databaseName, $tableName, $pathToOutputFolder);
        }
    }

    private function getOutputGateway(): PHPFileOutput
    {
        $outputFileConfig = $this->getOutputConfigFile();

        if ($outputFileConfig->hasCustomOutputClassname()) {
            $fqn = $outputFileConfig->getCustomOutputClassname();
            return new $fqn($outputFileConfig);
        }

        return new PHPFileOutput($outputFileConfig);
    }

    private function getOutputConfigFile(): OutputConfig
    {
        $outputFileConfig = new OutputConfig();
        $customTemplatePath = $this->getOpt->getOption('customTemplate');
        if (isset($customTemplatePath)) {
            $outputFileConfig->setCustomTemplatePath($customTemplatePath);
        }
        $customOutputFileGateway = $this->getOpt->getOption('customOutputFileGateway');
        if (isset($customTemplatePath)) {
            $outputFileConfig->setCustomOutputClassname($customOutputFileGateway);
        }
        return $outputFileConfig;
    }

    private function getDatabaseConfig(): InputConfig
    {
        $xmlConfigFilepath = $this->getOpt->getOption('xml-config-file');
        if (isset($xmlConfigFilepath)) {
            return $this->getDBConfigFromXMLFile($xmlConfigFilepath);
        }
        return $this->getDBConfigFromOptions();
    }

    private function getDBConfigFromXMLFile($xmlConfigFilepath) : InputConfig
    {
        $xml = $this->loadXMLFile($xmlConfigFilepath);
        $errorMessagePrefix = 'Error in xml-config-file "'.$xmlConfigFilepath.'": ';
        if (!isset($xml->type)) {
            die($errorMessagePrefix.'No (DBMS-)type given!'.PHP_EOL);
        }
        switch ((string)$xml->type) {
            case 'mysql':
                if (!isset($xml->hostname)) {
                    die($errorMessagePrefix.'No hostname given!'.PHP_EOL);
                }
                if (!isset($xml->username)) {
                    die($errorMessagePrefix.'No username given!'.PHP_EOL);
                }
                if (!isset($xml->password)) {
                    die($errorMessagePrefix.'No password given!'.PHP_EOL);
                }
                $port = $xml->port ?? 3306;
                return new MySQLInputConfig((string)$xml->username, (string)$xml->password, (string)$xml->hostname, (int)$port);

            // Add your dbms xml code here

            default:
                die($errorMessagePrefix.'Unknown (DBMS-)type "'.(string)$xml->type.'"!'.PHP_EOL);
        }



    }

    /**
     * @param $xmlConfigFilepath
     * @return \SimpleXMLElement
     */
    private function loadXMLFile($xmlConfigFilepath): \SimpleXMLElement
    {
        if (!file_exists($xmlConfigFilepath)) {
            die('Error: xml-config-file "' . $xmlConfigFilepath . '" not found!' . PHP_EOL);
        }
        if (!is_readable($xmlConfigFilepath)) {
            die('Error: xml-config-file "' . $xmlConfigFilepath . '" is not readable!' . PHP_EOL);
        }
        $xml = simplexml_load_file($xmlConfigFilepath);
        return $xml;
    }

    private function getDBConfigFromOptions() : InputConfig
    {
        $dbms = $this->getOpt->getOption('dbms');

        switch ($dbms) {

            case 'mysql':
                $hostname = $this->getOpt->getOption('mysql-host');
                if(is_null($hostname) || !strlen($hostname)) {
                    die('Error: DBMS "mysql" requires option "--mysql-host"!'.PHP_EOL);
                }
                $username = $this->getOpt->getOption('mysql-user');
                if(is_null($username) || !strlen($username)) {
                    die('Error: DBMS "mysql" requires option "--mysql-user"!'.PHP_EOL);
                }
                $password = $this->getOpt->getOption('mysql-password');
                if(is_null($password)) {
                    die('Error: DBMS "mysql" requires option "--mysql-password"!'.PHP_EOL);
                }
                $port = $this->getOpt->getOption('mysql-port');
                if(is_null($port)) {
                    die('Error: DBMS "mysql" requires option "--mysql-port"!'.PHP_EOL);
                }
                return new MySQLInputConfig($username, $password, $hostname, (int)$port);

            // Add your dbms case here and validate its options

            default:
                die('Error: Unknown dbms type "'.$dbms.'"!'.PHP_EOL);

        }
    }

    protected function getDatabaseName() : string
    {
        $databaseName = $this->getOpt->getOperand('database-name');
        if (!isset($databaseName) || !strlen($databaseName)) {
            die('Error: Missing database-name!' . PHP_EOL);
        }
        return $databaseName;
    }

}