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
    }

    public function getHelpText() : string
    {
        return $this->getOpt->getHelpText();
    }

    private function addGetOptDBMSOptions()
    {
        $this->getOpt->addOptions([
            // General options:
            \GetOpt\Option::create('?', 'help', \GetOpt\GetOpt::NO_ARGUMENT)
                ->setDescription('Print this help text'),

            // MySQL Specific options
            \GetOpt\Option::create('h', 'mysql-host', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Hostname'),
            \GetOpt\Option::create('u', 'mysql-user', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Username'),
            \GetOpt\Option::create('p', 'mysql-password', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Password'),
            \GetOpt\Option::create(null, 'mysql-port', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('MySQL Port')
                ->setDefaultValue(3306)
                ->setValidation('is_numeric'),

            // Output related options
            \GetOpt\Option::create(null, 'output-phpversion', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('PHP-Version to use in output (used in php templates)')
                ->setDefaultValue(PHP_VERSION),
            \GetOpt\Option::create(null, 'output-namespace', \GetOpt\GetOpt::OPTIONAL_ARGUMENT)
                ->setDescription('Namespace to use in output (used in php templates)')
        ]);

        // Add your dbms options here..
    }

    public function hasSetOptionHelp() : bool
    {
        global $argv;
        return in_array('-?', $argv) || in_array('--help', $argv);
    }

    public function run() {
        // Show help text?
        if($this->hasSetOptionHelp()) {
            echo $this->getOpt->getHelpText();
            exit(0);
        }

        // In case of illegal command line usage we want to render only the error message, and after that the help text:
        try {
            $this->getOpt->process();
        } catch (\Throwable $e) {
            echo "Error: ".$e->getMessage().PHP_EOL;
            echo PHP_EOL;
            echo $this->getHelpText();
            exit(0);
        }

        $this->process();
    }

    private function process()
    {
        $outputGateway = $this->getOutputGateway();
        $service = new ConvertService($outputGateway);
        $dbConfig = $this->getDatabaseConfig();
        $databaseName = $this->getDatabaseName();


        if($this->getOpt->getOption("help")) {
            $this->getHelpText();
            exit;
        }

        $pathToOutputFolder = $this->getOpt->getOperand('pathToOutputFolder');
        $tableName = $this->getOpt->getOperand('table');

        if($tableName !== null) {
            echo 'Converting table "'.$databaseName.'.'.$tableName.'" to output-folder "'.$pathToOutputFolder.'"'.PHP_EOL;
            $service->convertTable($dbConfig, $databaseName, $tableName, $pathToOutputFolder);
        } else {
            echo 'Converting database "'.$databaseName.'" to output-folder "'.$pathToOutputFolder.'"'.PHP_EOL;
            $service->convertDatabase($dbConfig, $databaseName, $pathToOutputFolder);
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
        if (isset($customOutputFileGateway)) {
            $outputFileConfig->setCustomOutputClassname($customOutputFileGateway);
        }

        $outputPHPVersion = $this->getOpt->getOption('output-phpversion');
        $outputFileConfig->setPhpVersion($outputPHPVersion);

        $outputNamespace = $this->getOpt->getOption('output-namespace');
        if (isset($outputNamespace)) {
            $outputFileConfig->setNamespace($outputNamespace);
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
        return simplexml_load_string(file_get_contents($xmlConfigFilepath));
    }

    private function getDBConfigFromOptions() : InputConfig
    {
        $dbms = $this->getOpt->getOption('dbms');

        switch ($dbms) {

            case 'mysql':
                $hostname = $this->getOpt->getOption('mysql-host');
                if($hostname === null || !strlen($hostname)) {
                    die('Error: DBMS "mysql" requires option "--mysql-host"!'.PHP_EOL);
                }
                $username = $this->getOpt->getOption('mysql-user');
                if($username === null || !strlen($username)) {
                    die('Error: DBMS "mysql" requires option "--mysql-user"!'.PHP_EOL);
                }
                $password = $this->getOpt->getOption('mysql-password');
                if($password === null) {
                    die('Error: DBMS "mysql" requires option "--mysql-password"!'.PHP_EOL);
                }
                $port = $this->getOpt->getOption('mysql-port');
                if($port === null) {
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