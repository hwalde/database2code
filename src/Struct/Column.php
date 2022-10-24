<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Struct;


use Database2Code\Struct\ColumnType\AbstractColumnType;

class Column
{
    /** @var string $name */
    protected $name;

    /** @var string $description */
    protected $description;

    /** @var $type AbstractColumnType */
    protected $type;

    /** @var $isNullable bool */
    protected $isNullable;

    /** @var $isPartOfPrimaryKey bool */
    protected $isPartOfPrimaryKey;

    public function __construct(string $name, AbstractColumnType $type, bool $isNullable, bool $isPartOfPrimaryKey = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->isPartOfPrimaryKey = $isPartOfPrimaryKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getType(): AbstractColumnType
    {
        return $this->type;
    }

    public function setType(AbstractColumnType $type)
    {
        $this->type = $type;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function setIsNullable(bool $isNullable)
    {
        $this->isNullable = $isNullable;
    }

    public function isPartOfPrimaryKey(): bool
    {
        return $this->isPartOfPrimaryKey;
    }

    public function setIsPartOfPrimaryKey(bool $isPartOfPrimaryKey): void
    {
        $this->isPartOfPrimaryKey = $isPartOfPrimaryKey;
    }
}