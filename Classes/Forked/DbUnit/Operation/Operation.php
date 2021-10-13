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

/**
 * Provides a basic interface and functionality for executing database
 * operations against a connection using a specific dataSet.
 */
interface Operation
{
    /**
     * Executes the database operation against the given $connection for the
     * given $dataSet.
     *
     * @param Connection $connection
     * @param IDataSet   $dataSet
     *
     * @throws Exception
     */
    public function execute(Connection $connection, IDataSet $dataSet);
}
