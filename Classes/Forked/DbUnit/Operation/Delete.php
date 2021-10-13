<?php
namespace PunktDe\Testing\Forked\DbUnit\Operation;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Database\Connection;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITableMetadata;

/**
 * Deletes the rows in a given dataset using primary key columns.
 */
class Delete extends RowBased
{
    protected $operationName = 'DELETE';

    protected $iteratorDirection = self::ITERATOR_TYPE_REVERSE;

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection)
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . \implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        $query = "
            DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            {$whereStatement}
        ";

        return $query;
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row)
    {
        $args = [];
        foreach ($databaseTableMetaData->getPrimaryKeys() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }
}
