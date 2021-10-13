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
 * The default implementation of table meta data
 */
class DefaultTableMetadata extends AbstractTableMetadata
{
    /**
     * Creates a new default table meta data object.
     *
     * @param string $tableName
     * @param array  $columns
     * @param array  $primaryKeys
     */
    public function __construct($tableName, array $columns, array $primaryKeys = [])
    {
        $this->tableName   = $tableName;
        $this->columns     = $columns;
        $this->primaryKeys = [];

        foreach ($primaryKeys as $columnName) {
            if (!\in_array($columnName, $this->columns)) {
                throw new InvalidArgumentException('Primary key column passed that is not in the column list.');
            } else {
                $this->primaryKeys[] = $columnName;
            }
        }
    }
}
