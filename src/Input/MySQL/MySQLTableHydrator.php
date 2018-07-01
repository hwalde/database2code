<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Input\MySQL;


use Database2Code\Struct\ColumnType\AbstractColumnType;
use Database2Code\Struct\ColumnType\DateColumnType;
use Database2Code\Struct\ColumnType\DatetimeColumnType;
use Database2Code\Struct\ColumnType\IntegerColumnType;
use Database2Code\Struct\ColumnType\StringColumnType;
use Database2Code\Struct\Column;
use Database2Code\Struct\ColumnType\UnknownColumnType;
use Database2Code\Struct\Table;

class MySQLTableHydrator
{
    public function hydrate(string $tableName, array $data) : Table
    {
       $table = new Table($tableName);
       $this->addColumns($table, $data);
       return $table;
    }

    private function addColumns(Table $table, array $data)
    {
        foreach ($data as $column) {
            $columnName = $this->getColumnName($column);
            $columnType = $this->getColumnType($column);
            $table->addColumn(new Column($columnName, $columnType));
        }
    }

    /**
     * @throws \ErrorException
     */
    private function getColumnName(array $column): string
    {
        if (!isset($column['Field'])) {
            throw new \ErrorException('Missing key "Field" in database-result-row');
        }
        return $column['Field'];
    }

    private function getColumnType(array $column) : AbstractColumnType
    {
        if (!isset($column['Type'])) {
            throw new \ErrorException('Missing key "Type" in database-result-row');
        }
        list($typeName, $typeLength) = $this->extractTypeNameAndLength($column['Type']);
        return $this->getColumnTypeObject($typeName, $column['Type'], $typeLength);
    }

    /**
     * @param string $mysqlTypeDefinition
     * @return array(name, length)
     * @throws \ErrorException
     */
    private function extractTypeNameAndLength(string $mysqlTypeDefinition): array
    {
        $parts = explode(' ', $mysqlTypeDefinition);
        $matches = null;
        if (!preg_match('#([a-z]+)(\(([0-9]+)\)|)#is', $parts[0], $matches)) {
            throw new \ErrorException('Failed to parse type information "' . $parts[0] . '"!');
        }
        $typeName = $matches[1];
        $typeLength = -1;
        if (isset($matches[3])) {
            $typeLength = (int)$matches[3];
        }
        return array($typeName, $typeLength);
    }

    private function getColumnTypeObject(string $typeName, string $mysqlTypeDefinition, int $typeLength) : AbstractColumnType
    {
        switch ($typeName) {
            case 'int':
            case 'mediumint':
            case 'tinyint':
                /*
tinyint(size)
smallint(size)
mediumint(size)
int(size)
bigint(size)
float(size,d)
double(size,d)
decimal(size,d)
                 */
            //case '':
                $columnType = new IntegerColumnType($mysqlTypeDefinition, $typeLength);
                break;
            case 'varchar': // has size
            case 'char': // has size
            case 'tinytext':
            case 'text':
            case 'blob':
            case 'mediumtext':
            case 'mediumblob':
            case 'longtext':
            case 'longblob':
            case 'enum': // enum(x,y,z,etc.)
            case 'set':
                $columnType = new StringColumnType($mysqlTypeDefinition);
                break;
            case 'date':
                $columnType = new DateColumnType($mysqlTypeDefinition);
                break;
            case 'datetime':
                $columnType = new DatetimeColumnType($mysqlTypeDefinition);
                break;
                /*
date()
datetime()
timestamp()
time()
year()
                 */
            default:
                $columnType = new UnknownColumnType($mysqlTypeDefinition);
        }
        return $columnType;
    }
}