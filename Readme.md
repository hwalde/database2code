# Database2Code

Database2Code is a tool to dump the structure of your database, or single tables into sourcecode (e.g. PHP) files or potential other formats.

It is the PHP equivalent to the code generator from jOOQ.

## Purpose

Do you want auto-completion on table and column names? Like in the following PHP example:

```php

$sql = 'SELECT '.s_core_engine_groups::variantable.' FROM '.s_core_engine_groups::class;
```

Then you need a tool to dump you database structure into php code. 

This tool will do that for you!

You can use it either as command line program or use the PHP API. 

## Installation

Use [Composer](https://getcomposer.org/) to download and install Database2Code. 

```bash
$ composer require hwalde/database2code
```

## Usage

### Usage from command line

Convert all tables of a database:
```bash
$ php database2code \ 
    --dbms='mysql' \
    --mysql-host='localhost' \
    --mysql-user='username' \ 
    --mysql-password='password' \
    'pathToOutputFolder' 'database_name'
```

Convert only one table:
```bash
$ php database2code \ 
    --dbms='mysql' \
    --mysql-host='localhost' \
    --mysql-user='username' \ 
    --mysql-password='password' \
    'pathToOutputFolder' 'database_name' 'table_name'
```

#### Options

```--mysql-port [3306]``` MySQL port

```--customTemplate [filePath]``` Use a custom output-file template

```--customOutputFileGateway [FQN]``` Use can specify a custom output-file generator

```--xml-config-file [filePath]``` Read database config from an xml file

#### XML database configuration

Instead of using the command line options to set database entries you can read them from an xml file instead.
Simply use the "--xml-config-file" to set the filepath to the xml file.

Fileformat:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<database>
    <type>mysql</type>
    <hostname>hostname</hostname>
    <username>the-username</username>
    <password>the-password</password>
    <port>3306</port>
</database>
```

The port is optional!
The Fileformat may vary depending on the type.

### Using the PHP API

```php
$outputConfig = new \Database2Code\Output\OutputConfig();

// Optionally set your own (or some provided) template:
$outputConfig->setCustomTemplatePath('Template/PHPFile/getterAndSetter.php');

// And in case an own template is not enough customization, then you can provide an your own output-class.
// Note: Your output-class needs to implement \Database2Code\Output\Output interface.
// The required argument is the fully qualified classname to your output-class:
$outputConfig->setCustomOutputClassname(MyOutputOutput::class);

$output = new \Database2Code\Output\PHPFile\PHPFileOutput($outputConfig);
$service = new \Database2Code\Service\ConvertService($output);

$inputConfig = new \Database2Code\Input\MySQL\MySQLInputConfig('username', 'password', 'hostname');

// Convert entire database:
$service->convertDatabase($inputConfig, 'database-name', 'output/folder/path');

// Convert single table:
$service->convertTable($inputConfig, 'database-name', 'table-name', 'output/folder/path');
```

## Extending and Customizing

### Adding another DBMS

Currently only MySQL is implemented. But adding other DBMS types (SQLite, Oracle, ...) is easy.

All you have to do is (lets assume we want to add PostgreSQL):
 
 1. Create a class that will contain the connection information ```Input/PostgreSQL/PostgreSQLInputConfig.php``` that implements ```Input/InputConfig```. Since in this case it is PostgreSQL you ca probably copy 99% from ```MySQLInputConfig.php```
 2. Create a ```Input/PostgreSQL/PostgreSQLInput.php``` class that implements ```DBMSGatewayInterface```. 
 3. Add the new DBMS to ```Service/ConvertService.php```: 
    ```php
    use Database2Code\Input\PostgreSQL\PostgreSQLInputConfig;
    use Database2Code\Input\PostgreSQL\PostgreSQLInput;
    
    private function generateInputInstance(InputConfig $inputConfig, string $database) : Input
    {
        if ($inputConfig instanceof MySQLInputConfig) {
            return new MySQLInput($inputConfig, $database, new MySQLTableHydrator());
        }
        if ($inputConfig instanceof PostgreSQLInputConfig) {
            return new PostgreSQLGateway($inputConfig);
        }
        throw new \Error('Unknown InputConfig "' . get_class($dbConfig) . '"!');
    }
    ```
 4. Add it to ```Console/Application.php```
 5. Create a pull request on Github
 
## Appendix

### <a name="furtherPurpose"></a>More on the purpose of this tool

##### This is useful combined with an Database-API:

When you already use auto-completion while use an api to access your database, then you will find it comfortable doing that with the table and column names as well:

```php
$rows = $db->select('*')
    ->from(s_core_engine_groups::class)
    ->whereIsTrue(s_core_engine_groups::variantable)
    ->fetchAll();
    
foreach($rows as $row) {
    echo $row[s_core_engine_groups::name];   
}
```

##### What if names change?

Now imagine you want to change the column name "variantable" to something else. 
Usually that would mean changing it in the database and than rewriting all the sourcefiles of your project where that name is used. Hoping that you don't miss out some part. Or alternatively using Search&Replace. But with general purpose names this only leads to disaster.

This doesn't have to be! 

Using objects instead of strings..
1. you have successfully decoupled your codebase from the actual name in your database and change the used name by changing the model instead of you codebase. (Change one file instead of numerous ones.)
2. you can use the Refactor->Rename feature of your IDE to make your code look pretty again. You can easily improve readability whenever you like. Whenever you want to know the actuall name of a column you can always hover it with your cursor. You IDE then will display it to you. 




