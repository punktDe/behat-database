<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

/**
 * Provides basic functionality for table meta data.
 */
abstract class AbstractTableMetadata implements ITableMetadata
{
    /**
     * The names of all columns in the table.
     *
     * @var array
     */
    protected $columns;

    /**
     * The names of all the primary keys in the table.
     *
     * @var array
     */
    protected $primaryKeys;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     *
     * @param ITableMetadata $other
     */
    public function matches(ITableMetadata $other)
    {
        if ($this->getTableName() != $other->getTableName() ||
            $this->getColumns() != $other->getColumns()
        ) {
            return false;
        }

        return true;
    }
}
