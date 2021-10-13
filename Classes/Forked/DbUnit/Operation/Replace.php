<?php
namespace PunktDe\Testing\Forked\DbUnit\Operation;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Database\Connection;
use PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITableMetadata;

/**
 * Updates the rows in a given dataset using primary key columns.
 */
class Replace extends RowBased
{
    protected $operationName = 'REPLACE';

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection)
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . \implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        $query = "
            SELECT COUNT(*)
            FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
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

    /**
     * @param Connection $connection
     * @param IDataSet   $dataSet
     */
    public function execute(Connection $connection, IDataSet $dataSet)
    {
        $insertOperation = new Insert;
        $updateOperation = new Update;
        $databaseDataSet = $connection->createDataSet();

        foreach ($dataSet as $table) {
            /* @var $table ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());

            $insertQuery = $insertOperation->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $updateQuery = $updateOperation->buildOperationQuery($databaseTableMetaData, $table, $connection);
            $selectQuery = $this->buildOperationQuery($databaseTableMetaData, $table, $connection);

            $insertStatement = $connection->getConnection()->prepare($insertQuery);
            $updateStatement = $connection->getConnection()->prepare($updateQuery);
            $selectStatement = $connection->getConnection()->prepare($selectQuery);

            $rowCount = $table->getRowCount();

            for ($i = 0; $i < $rowCount; $i++) {
                $selectArgs = $this->buildOperationArguments($databaseTableMetaData, $table, $i);
                $query      = $selectQuery;
                $args       = $selectArgs;

                try {
                    $selectStatement->execute($selectArgs);

                    if ($selectStatement->fetchColumn(0) > 0) {
                        $updateArgs = $updateOperation->buildOperationArguments($databaseTableMetaData, $table, $i);
                        $query      = $updateQuery;
                        $args       = $updateArgs;

                        $updateStatement->execute($updateArgs);
                    } else {
                        $insertArgs = $insertOperation->buildOperationArguments($databaseTableMetaData, $table, $i);
                        $query      = $insertQuery;
                        $args       = $insertArgs;

                        $insertStatement->execute($insertArgs);
                    }
                } catch (\Exception $e) {
                    throw new Exception(
                        $this->operationName, $query, $args, $table, $e->getMessage()
                    );
                }
            }
        }
    }
}