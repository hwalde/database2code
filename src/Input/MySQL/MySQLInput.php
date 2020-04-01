<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Input\MySQL;


use Database2Code\Struct\Table;

class MySQLInput implements \Database2Code\Input\Input
{
    /** @var $pdo \PDO */
    private $pdo;

    /** @var $hydrator \Database2Code\Input\MySQL\MySQLTableHydrator */
    private $hydrator;

    public function __construct(MySQLInputConfig $config, string $databaseName, MySQLTableHydrator $hydrator)
    {
        $this->pdo = $this->createPDOInstance($config, $databaseName);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->hydrator = $hydrator;
    }

    public function listTables(): array
    {
        $stmt = $this->pdo->query('SHOW TABLES');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    public function getTableStruct(string $name): Table
    {
        $sql = 'SHOW COLUMNS FROM '.$this->escapeIdentifier($name);
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->hydrator->hydrate($name, $rows);
    }

    private function createPDOInstance(MySQLInputConfig $config, string $databaseName): \PDO
    {
        $dns = 'mysql:host=' . $config->getHostname() . ':' . $config->getPort() . ';dbname=' . $databaseName;
        return new \PDO($dns, $config->getUsername(), $config->getPassword(), $config->getPdoOptions());
    }

    /**
     * @todo Improve this method
     */
    private function escapeIdentifier(string $subject) : string
    {
        return '`'.$subject.'`';
    }
}