<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Struct;


class Table
{
    /** @var $name string */
    protected $name;

    /** @var $columns Column[] */
    protected $columns;

    public function __construct($name)
    {
        $this->name = $name;
        $this->columns = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
    }

    public function addColumn(Column $column) {
        $this->columns[] = $column;
    }

    public function containsPrimaryKey() : bool
    {
        return count($this->getPrimaryKeyColumnList()) === 0;
    }

    /**
     * @return Column[]
     */
    public function getPrimaryKeyColumnList(): array
    {
        $list = [];
        foreach ($this->columns as $column) {
            if($column->isPartOfPrimaryKey()) {
                $list[] = $column;
            }
        }
        return $list;
    }

}