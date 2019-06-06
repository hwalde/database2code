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
    /**
     * @param string $tableName
     * @param array $rows Database rows
     * @return Table
     * @throws \ErrorException
     */
    public function hydrate(string $tableName, array $rows) : Table
    {
       $table = new Table($tableName);
       $this->addColumns($table, $rows);
       return $table;
    }

    /**
     * @param Table $table Object to hydrate
     * @param array $rows Database rows
     * @throws \ErrorException
     */
    private function addColumns(Table $table, array $rows)
    {
        foreach ($rows as $column) {
            $columnName = $this->getColumnName($column);
            $columnType = $this->getColumnType($column);
            $isNullable = $this->isColumnNullable($column);
            $isPartOfPrimaryKey = $this->isColumnPartOfPrimaryKey($column);
            $table->addColumn(new Column($columnName, $columnType, $isNullable, $isPartOfPrimaryKey));
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

    /**
     * @param array $column
     * @return bool
     * @throws \ErrorException
     */
    private function isColumnNullable(array $column) : bool
    {
        if (!isset($column['Null'])) {
            throw new \ErrorException('Missing key "Null" in database-result-row');
        }
        switch (strtoupper($column['Null'])) {
            case 'YES':
                return true;
                break;
            case 'NO':
                return false;
                break;
            default:
                throw new \ErrorException('Unknown Null-value "'.$column['Null'].'" in database-result-ro');
        }
    }

    /**
     * @param array $column
     * @return AbstractColumnType
     * @throws \ErrorException
     */
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
            case 'smallint':
            case 'bigint':
                /*
float(size,d)
double(size,d)
decimal(size,d)
                 */
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
timestamp()
time()
year()
                 */
            default:
                $columnType = new UnknownColumnType($mysqlTypeDefinition);
        }
        return $columnType;
    }

    private function isColumnPartOfPrimaryKey($column)
    {
        if (!isset($column['Key'])) {
            throw new \ErrorException('Missing key "Key" in database-result-row');
        }
        return $column['Key']=='PRI';
    }
}