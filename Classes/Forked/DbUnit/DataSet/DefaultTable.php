<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Exception\InvalidArgumentException;

/**
 * Provides default table functionality.
 */
class DefaultTable extends AbstractTable
{
    /**
     * Creates a new table object using the given $tableMetaData
     *
     * @param ITableMetadata $tableMetaData
     */
    public function __construct(ITableMetadata $tableMetaData)
    {
        $this->setTableMetaData($tableMetaData);
        $this->data = [];
    }

    /**
     * Adds a row to the table with optional values.
     *
     * @param array $values
     */
    public function addRow($values = [])
    {
        $this->data[] = \array_replace(
            \array_fill_keys($this->getTableMetaData()->getColumns(), null),
            $values
        );
    }

    /**
     * Adds the rows in the passed table to the current table.
     *
     * @param ITable $table
     */
    public function addTableRows(ITable $table)
    {
        $tableColumns = $this->getTableMetaData()->getColumns();
        $rowCount     = $table->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $newRow = [];
            foreach ($tableColumns as $columnName) {
                $newRow[$columnName] = $table->getValue($i, $columnName);
            }
            $this->addRow($newRow);
        }
    }

    /**
     * Sets the specified column of the specied row to the specified value.
     *
     * @param int    $row
     * @param string $column
     * @param mixed  $value
     */
    public function setValue($row, $column, $value)
    {
        if (isset($this->data[$row])) {
            $this->data[$row][$column] = $value;
        } else {
            throw new InvalidArgumentException('The row given does not exist.');
        }
    }
}