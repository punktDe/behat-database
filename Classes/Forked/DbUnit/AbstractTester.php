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
use PunktDe\Testing\Forked\DbUnit\Operation\Factory;
use PunktDe\Testing\Forked\DbUnit\Operation\Operation;

/**
 * Can be used as a foundation for new DatabaseTesters.
 */
abstract class AbstractTester implements Tester
{
    /**
     * @var Operation
     */
    protected $setUpOperation;

    /**
     * @var Operation
     */
    protected $tearDownOperation;

    /**
     * @var IDataSet
     */
    protected $dataSet;

    /**
     * @var string
     */
    protected $schema;

    /**
     * Creates a new database tester.
     */
    public function __construct()
    {
        $this->setUpOperation    = Factory::CLEAN_INSERT();
        $this->tearDownOperation = Factory::NONE();
    }

    /**
     * Closes the specified connection.
     *
     * @param Connection $connection
     */
    public function closeConnection(Connection $connection)
    {
        $connection->close();
    }

    /**
     * Returns the test dataset.
     *
     * @return IDataSet
     */
    public function getDataSet()
    {
        return $this->dataSet;
    }

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp()
    {
        $this->getSetUpOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown()
    {
        $this->getTearDownOperation()->execute($this->getConnection(), $this->getDataSet());
    }

    /**
     * Sets the test dataset to use.
     *
     * @param IDataSet $dataSet
     */
    public function setDataSet(IDataSet $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    /**
     * Sets the schema value.
     *
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * Sets the DatabaseOperation to call when starting the test.
     *
     * @param Operation $setUpOperation
     */
    public function setSetUpOperation(Operation $setUpOperation)
    {
        $this->setUpOperation = $setUpOperation;
    }

    /**
     * Sets the DatabaseOperation to call when ending the test.
     *
     * @param Operation $tearDownOperation
     */
    public function setTearDownOperation(Operation $tearDownOperation)
    {
        $this->tearDownOperation = $tearDownOperation;
    }

    /**
     * Returns the schema value
     *
     * @return string
     */
    protected function getSchema()
    {
        return $this->schema;
    }

    /**
     * Returns the database operation that will be called when starting the test.
     *
     * @return Operation
     */
    protected function getSetUpOperation()
    {
        return $this->setUpOperation;
    }

    /**
     * Returns the database operation that will be called when ending the test.
     *
     * @return Operation
     */
    protected function getTearDownOperation()
    {
        return $this->tearDownOperation;
    }
}
