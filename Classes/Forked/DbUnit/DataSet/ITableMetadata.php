<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

/**
 * Provides a basic interface for returning table meta data.
 */
interface ITableMetadata
{
    /**
     * Returns the names of the columns in the table.
     *
     * @return array
     */
    public function getColumns();

    /**
     * Returns the names of the primary key columns in the table.
     *
     * @return array
     */
    public function getPrimaryKeys();

    /**
     * Returns the name of the table.
     *
     * @return string
     */
    public function getTableName();

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     *
     * @param ITableMetadata $other
     */
    public function matches(ITableMetadata $other);
}
