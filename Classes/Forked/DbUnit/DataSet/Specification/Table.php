<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PunktDe\Behat\Database\Forked\DbUnit\DataSet\Specification;

use PunktDe\Behat\Database\Forked\DbUnit\Database\DefaultConnection;
use PunktDe\Behat\Database\Forked\DbUnit\DatabaseListConsumer;
use PunktDe\Behat\Database\Forked\DbUnit\DataSet\IDataSet;
use ReflectionClass;

/**
 * Creates a database dataset based off of a spec string.
 *
 * This spec class requires a list of databases to be set to the object before
 * it can return a list of databases.
 *
 * The format of the spec string is as follows:
 *
 * <db label>:<schema>:<tables>
 *
 * The db label should be equal to one of the keys in the array of databases
 * passed to setDatabases().
 *
 * The schema should be the primary schema you will be choosing tables from.
 *
 * The tables should be a comma delimited list of all tables you would like to
 * pull data from.
 *
 * The sql is the query you want to use to generate the table columns and data.
 * The column names in the table will be identical to the column aliases in the
 * query.
 */
class Table implements Specification, DatabaseListConsumer
{
    /**
     * @var array
     */
    protected $databases = [];

    /**
     * Sets the database for the spec
     *
     * @param array $databases
     */
    public function setDatabases(array $databases)
    {
        $this->databases = $databases;
    }

    /**
     * Creates a DB Data Set from a data set spec.
     *
     * @param string $dataSetSpec
     *
     * @return IDataSet
     */
    public function getDataSet($dataSetSpec)
    {
        list($dbLabel, $schema, $tables) = \explode(':', $dataSetSpec, 3);
        $databaseInfo                    = $this->databases[$dbLabel];

        $pdoRflc      = new ReflectionClass('PDO');
        $pdo          = $pdoRflc->newInstanceArgs(\explode('|', $databaseInfo));
        $dbConnection = new DefaultConnection($pdo, $schema);

        return !empty($tables) ? $dbConnection->createDataSet(\explode(',', $tables)) : $dbConnection->createDataSet();
    }
}
