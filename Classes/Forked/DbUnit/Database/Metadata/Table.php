<?php
namespace PunktDe\Testing\Forked\DbUnit\Database\Metadata;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\DataSet\DefaultTableMetadata;

/**
 * This class loads a table metadata object with database metadata.
 */
class Table extends DefaultTableMetadata
{
    public function __construct($tableName, Metadata $databaseMetaData)
    {
        $this->tableName   = $tableName;
        $this->columns     = $databaseMetaData->getTableColumns($tableName);
        $this->primaryKeys = $databaseMetaData->getTablePrimaryKeys($tableName);
    }
}
