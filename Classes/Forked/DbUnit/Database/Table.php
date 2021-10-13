<?php
namespace PunktDe\Testing\Forked\DbUnit\Database;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PDO;
use PunktDe\Testing\Forked\DbUnit\DataSet\AbstractTable;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITableMetadata;

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
