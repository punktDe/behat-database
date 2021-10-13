<?php
namespace PunktDe\Testing\Forked\DbUnit;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Database\Connection;
use PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Testing\Forked\DbUnit\Operation\Operation;

/**
 * This is the interface for DatabaseTester objects. These objects are used to
 * add database testing to existing test cases using composition instead of
 * extension.
 */
interface Tester
{
    /**
     * Closes the specified connection.
     *
     * @param Connection $connection
     */
    public function closeConnection(Connection $connection);

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    public function getConnection();

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    public function getDataSet();

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp();

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown();

    /**
     * Sets the test dataset to use.
     *
     * @param IDataSet $dataSet
     */
    public function setDataSet(IDataSet $dataSet);

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema);

    /**
     * Sets the DatabaseOperation to call when starting the test.
     *
     * @param Operation $setUpOperation
     */
    public function setSetUpOperation(Operation $setUpOperation);

    /**
     * Sets the DatabaseOperation to call when stopping the test.
     *
     * @param Operation $tearDownOperation
     */
    public function setTearDownOperation(Operation $tearDownOperation);
}
