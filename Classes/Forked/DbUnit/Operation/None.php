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
 * This class represents a null database operation.
 */
class None implements Operation
{
    public function execute(Connection $connection, IDataSet $dataSet)
    {
        /* do nothing */
    }
}
