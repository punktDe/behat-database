<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PunktDe\Behat\Database\Forked\DbUnit\Operation;

use PunktDe\Behat\Database\Forked\DbUnit\Database\Connection;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\IDataSet;

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
