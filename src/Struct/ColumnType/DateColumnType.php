<?php
/*
 * This file is part of Database2Code.
 *
 * (c) Herbert Walde <herbert.walde@gmail.com>
 *
 * For the full copyright and license information, read the LICENSE file that was distributed with this source code.
 */


namespace Database2Code\Struct\ColumnType;


class DateColumnType extends AbstractColumnType
{

    /**
     * @inheritdoc
     */
    public function getPseudoName(): string
    {
        return 'date';
    }

    /**
     * @inheritdoc
     */
    public function getPHPTypeName(): string
    {
        return '\DateTime';
    }

}