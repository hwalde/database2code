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

    public function __construct(string $name, AbstractColumnType $type)
    {
        $this->name = $name;
        $this->type = $type;
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

}