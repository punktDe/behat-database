<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use Iterator;

/**
 * Provides a basic interface for creating and reading data from data sets.
 */
interface ITableIterator extends Iterator
{
    /**
     * Returns the current table.
     *
     * @return ITable
     */
    public function getTable();

    /**
     * Returns the current table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData();
}
