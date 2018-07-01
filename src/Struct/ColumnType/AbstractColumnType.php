<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Struct\ColumnType;


abstract class AbstractColumnType
{

    /** @var $sqlRepresentation string */
    protected $sqlRepresentation;

    public function __construct($sqlRepresentation)
    {
        $this->sqlRepresentation = $sqlRepresentation;
    }

    /**
     * A unique name representing this type
     */
    abstract public function getPseudoName() : string;

    /**
     * PHP-type this type gets mapped to
     */
    abstract public function getPHPTypeName() : string;

    /**
     * The real dbms type (usually differs from DBMS to DBMS)
     */
    public function getSqlRepresentation() : string
    {
        return $this->sqlRepresentation;
    }

}