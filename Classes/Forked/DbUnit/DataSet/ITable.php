<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

/**
 * Provides a basic interface for creating and reading data from data sets.
 */
interface ITable
{
    /**
     * Returns the table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData();

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount();

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param int $column
     */
    public function getValue($row, $column);

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param int $row
     *
     * @return array
     */
    public function getRow($row);

    /**
     * Asserts that the given table matches this table.
     *
     * @param ITable $other
     */
    public function matches(ITable $other);
}
