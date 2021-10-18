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

use PDOException;
use PunktDe\Behat\Database\Forked\DbUnit\Database\Connection;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\ITable;

/**
 * Deletes all rows from all tables in a dataset.
 */
class DeleteAll implements Operation
{
    public function execute(Connection $connection, IDataSet $dataSet)
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table ITable */

            $query = "
                DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            ";

            try {
                $connection->getConnection()->query($query);
            } catch (PDOException $e) {
                throw new Exception('DELETE_ALL', $query, [], $table, $e->getMessage());
            }
        }
    }
}
