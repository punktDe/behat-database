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

use PDO;
use PDOException;
use PunktDe\Behat\Database\Forked\DbUnit\Database\Connection;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\ITable;

/**
 * Executes a truncate against all tables in a dataset.
 */
class Truncate implements Operation
{
    protected $useCascade = false;

    public function setCascade($cascade = true)
    {
        $this->useCascade = $cascade;
    }

    public function execute(Connection $connection, IDataSet $dataSet)
    {
        foreach ($dataSet->getReverseIterator() as $table) {
            /* @var $table ITable */
            $query = "
                {$connection->getTruncateCommand()} {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            ";

            if ($this->useCascade && $connection->allowsCascading()) {
                $query .= ' CASCADE';
            }

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->query($query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Exception $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof PDOException) {
                    throw new Exception('TRUNCATE', $query, [], $table, $e->getMessage());
                }

                throw $e;
            }
        }
    }

    private function disableForeignKeyChecksForMysql(Connection $connection)
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeyChecksForMysql(Connection $connection)
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->query('SET FOREIGN_KEY_CHECKS=@PHPUNIT_OLD_FOREIGN_KEY_CHECKS');
        }
    }

    private function isMysql(Connection $connection)
    {
        return $connection->getConnection()->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql';
    }
}
