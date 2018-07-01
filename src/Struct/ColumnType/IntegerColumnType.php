<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Struct\ColumnType;


class IntegerColumnType extends AbstractColumnType
{
    public function __construct(string $sqlRepresentation, int $length = -1)
    {
        parent::__construct($sqlRepresentation);
    }

    /**
     * @inheritdoc
     */
    public function getPseudoName(): string
    {
        return 'integer';
    }

    /**
     * @inheritdoc
     */
    public function getPHPTypeName(): string
    {
        return 'int';
    }

}