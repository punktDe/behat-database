<?php
namespace PunktDe\Testing\Forked\DbUnit\Database;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PDO;
use PunktDe\Testing\Forked\DbUnit\Database\Metadata\Metadata;
use PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;

/**
 * Provides a basic interface for communicating with a database.
 */
interface Connection
{
    /**
     * Close this connection.
     */
    public function close();

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     * @param array $tableNames
     *
     * @return IDataSet
     */
    public function createDataSet(array $tableNames = null);

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     *
     * @return ITable
     */
    public function createQueryTable($resultName, $sql);

    /**
     * Returns a PDO Connection
     *
     * @return PDO
     */
    public function getConnection();

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return Metadata
     */
    public function getMetaData();

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName
     * @param string $whereClause
     * @param int
     */
    public function getRowCount($tableName, $whereClause = null);

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema();

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject($object);

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand();

    /**
     * Returns true if the connection allows cascading
     *
     * @return bool
     */
    public function allowsCascading();

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     *
     * @param string $tableName
     */
    public function disablePrimaryKeys($tableName);

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys($tableName);
}
