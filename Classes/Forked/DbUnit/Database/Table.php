<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PunktDe\Behat\Database\Forked\DbUnit\Database;

use PDO;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\AbstractTable;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\ITableMetadata;

/**
 * Provides the functionality to represent a database table.
 */
class Table extends AbstractTable
{
    /**
     * Creates a new database table object.
     *
     * @param ITableMetadata $tableMetaData
     * @param Connection     $databaseConnection
     */
    public function __construct(ITableMetadata $tableMetaData, Connection $databaseConnection)
    {
        $this->setTableMetaData($tableMetaData);

        $pdoStatement = $databaseConnection->getConnection()->prepare(DataSet::buildTableSelect($tableMetaData, $databaseConnection));
        $pdoStatement->execute();
        $this->data = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);
    }
}
