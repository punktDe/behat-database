<?php
namespace PunktDe\Testing\Forked\DbUnit\Database;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\DataSet\AbstractDataSet;
use PunktDe\Testing\Forked\DbUnit\DataSet\DefaultTableMetadata;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITableMetadata;
use PunktDe\Testing\Forked\DbUnit\InvalidArgumentException;
use PunktDe\Testing\Forked\DbUnit\RuntimeException;

/**
 * Provides access to a database instance as a data set.
 */
class DataSet extends AbstractDataSet
{
    /**
     * An array of ITable objects.
     *
     * @var array
     */
    protected $tables = [];

    /**
     * The database connection this dataset is using.
     *
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * Creates a new dataset using the given database connection.
     *
     * @param Connection $databaseConnection
     */
    public function __construct(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Creates the query necessary to pull all of the data from a table.
     *
     * @param ITableMetadata $tableMetaData
     *
     * @return string
     */
    public static function buildTableSelect(ITableMetadata $tableMetaData, Connection $databaseConnection = null)
    {
        if ($tableMetaData->getTableName() == '') {
            $e = new RuntimeException('Empty Table Name');
            echo $e->getTraceAsString();
            throw $e;
        }

        $columns = $tableMetaData->getColumns();
        if ($databaseConnection) {
            $columns = \array_map([$databaseConnection, 'quoteSchemaObject'], $columns);
        }
        $columnList = \implode(', ', $columns);

        if ($databaseConnection) {
            $tableName = $databaseConnection->quoteSchemaObject($tableMetaData->getTableName());
        } else {
            $tableName = $tableMetaData->getTableName();
        }

        $primaryKeys = $tableMetaData->getPrimaryKeys();
        if ($databaseConnection) {
            $primaryKeys = \array_map([$databaseConnection, 'quoteSchemaObject'], $primaryKeys);
        }
        if (\count($primaryKeys)) {
            $orderBy = 'ORDER BY ' . \implode(' ASC, ', $primaryKeys) . ' ASC';
        } else {
            $orderBy = '';
        }

        return "SELECT {$columnList} FROM {$tableName} {$orderBy}";
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return TableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new TableIterator($this->getTableNames(), $this, $reverse);
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return Table
     */
    public function getTable($tableName)
    {
        if (!\in_array($tableName, $this->getTableNames())) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        if (empty($this->tables[$tableName])) {
            $this->tables[$tableName] = new Table($this->getTableMetaData($tableName), $this->databaseConnection);
        }

        return $this->tables[$tableName];
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     *
     * @return DefaultTableMetadata
     */
    public function getTableMetaData($tableName)
    {
        return new DefaultTableMetadata($tableName, $this->databaseConnection->getMetaData()->getTableColumns($tableName), $this->databaseConnection->getMetaData()->getTablePrimaryKeys($tableName));
    }

    /**
     * Returns a list of table names for the database
     *
     * @return array
     */
    public function getTableNames()
    {
        return $this->databaseConnection->getMetaData()->getTableNames();
    }
}
