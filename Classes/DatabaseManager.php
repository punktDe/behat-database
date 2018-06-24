<?php
namespace PunktDe\Behat\Database;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\AbstractDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use PHPUnit\DbUnit\DefaultTester;
use PHPUnit\DbUnit\Operation\Factory;
use PunktDe\Behat\Database\Utility\Replacement;
use Symfony\Component\Config\Definition\Exception\Exception;

class DatabaseManager
{
    /**
     * @var array
     */
    protected $connections;

    /**
     * @var array
     */
    protected $databaseCredentials;

    /**
     * @var Replacement
     */
    protected $replacement;

    /**
     * @param array $databaseCredentials
     * @param string $domainName
     */
    public function __construct(array $databaseCredentials, $domainName = null)
    {
        $this->databaseCredentials = $databaseCredentials;
        $this->replacement = new Replacement($domainName);
    }

    /**
     * @return void
     */
    protected function setupDatabaseConnectionsIfNeeded()
    {
        if ($this->connections === null) {
            foreach ($this->databaseCredentials as $schema => $credentials) {
                $databaseName = $schema;
                if (array_key_exists('database', $credentials)) {
                    $databaseName = $credentials['database'];
                }

                $this->connections[$schema] = new DefaultConnection(
                    new \PDO(
                        'mysql:dbname=' . $databaseName . ';host=' . $credentials['hostname'] . ';charset=utf8',
                        $credentials['username'],
                        $credentials['password']
                    ),
                    $databaseName
                );
            }
        }
    }

    /**
     * @param string $dataSetFilePath
     * @param string $schema
     * @throws SuiteConfigurationException
     * @return void
     * @throws \Exception
     */
    public function importDataSetToDatabase($dataSetFilePath, $schema)
    {
        if (!file_exists($dataSetFilePath)) {
            throw new SuiteConfigurationException(sprintf('No dataset found at path "%s"', $dataSetFilePath), 1407857990);
        }

        $dataSet = $this->prepareDataSetWithDataReplacement(new YamlDataSet($dataSetFilePath));

        $tester = new DefaultTester($this->getConnectionBySchema($schema));
        $tester->setSetUpOperation(Factory::CLEAN_INSERT());
        $tester->setDataSet($dataSet);
        $tester->onSetUp();
        $tester->closeConnection($this->getConnectionBySchema($schema));

        $this->connections = null;
    }

    /**
     * @param string $dataSetFilePath
     * @param string $schema
     * @throws SuiteConfigurationException
     * @return void
     * @throws \Exception
     */
    public function addDataSetToDatabase($dataSetFilePath, $schema)
    {
        if (!file_exists($dataSetFilePath)) {
            throw new SuiteConfigurationException(sprintf('No dataset found at path "%s"', $dataSetFilePath), 1410362300);
        }

        $dataSet = $this->prepareDataSetWithDataReplacement(new YamlDataSet($dataSetFilePath));

        $tester = new DefaultTester($this->getConnectionBySchema($schema));
        $tester->setSetUpOperation(Factory::INSERT());
        $tester->setDataSet($dataSet);
        $tester->onSetUp();
        $tester->closeConnection($this->getConnectionBySchema($schema));

        $this->connections = null;
    }

    /**
     * Imports the given dump file into the given database with the (optionally) given database name.
     *
     * If no database name is given the one (and probably only) database that is given in the credentials is used.
     *
     * @param string $dumpFilePath Full qualified path of the dump file to be imported
     * @param string $schema Name of the database into which the dump should be imported
     * @throws \Symfony\Component\Config\Definition\Exception\Exception
     */
    public function importDumpToDatabase($dumpFilePath, $schema = null)
    {
        if (!file_exists($dumpFilePath)) {
            throw new Exception('Database dump file not found:' . $dumpFilePath, 1409067298);
        }

        if ($schema === null) {
            if (count($this->databaseCredentials) > 1) {
                throw new Exception('You have configured more than one set of database credentials. Please define the schema name.', 1409310064);
            }

            reset($this->databaseCredentials);
            $schema = key($this->databaseCredentials);
        }

        if (!array_key_exists($schema, $this->databaseCredentials)) {
            throw new Exception("The given database credential name $schema was not found.", 1409310124);
        }

        $credentials = $this->databaseCredentials[$schema];

        if (array_key_exists('database', $credentials)) {
            $dbName = $credentials['database'];
        } else {
            $dbName = $schema;
        }

        echo sprintf("Importing SQL file %s to %s\n\n", $dumpFilePath, $dbName);

        $mysqlCommand = sprintf('mysql -u %s -h %s -p%s %s', $credentials['username'], $credentials['hostname'], $credentials['password'], $dbName);
        $dropAndCreateCommand = $mysqlCommand . ' -e \'DROP DATABASE IF EXISTS `' . $dbName . '`; CREATE DATABASE `' . $dbName . '`;\'';
        exec($dropAndCreateCommand);
        exec($mysqlCommand . ' < ' . $dumpFilePath);
    }

    /**
     * @return void
     */
    public function cleanConnections()
    {
        $this->connections = null;
    }

    /**
     * @param string $schema
     * @throws \Exception
     * @return DefaultConnection
     */
    public function getConnectionBySchema($schema)
    {
        $this->setupDatabaseConnectionsIfNeeded();
        if (!isset($this->connections[$schema]) || $this->connections[$schema] == null) {
            throw new \Exception(sprintf('No connection for database "%s" available. Aborting!', $schema), 1407419057);
        }
        return $this->connections[$schema];
    }

    /**
     * @return void
     */
    public function copyDatabase()
    {
        $commandTemplate = 'mysqldump -u %s -h %s -p%s --compress --skip-lock-tables --no-autocommit %s | mysql -u %s -h %s -p%s -D %s';
        echo sprintf("Importing database '%s' from '%s' to database '%s' on '%s'", $this->databaseCredentials['source']['database'], $this->databaseCredentials['source']['hostname'], $this->databaseCredentials['destination']['database'], $this->databaseCredentials['destination']['hostname']);
        exec(sprintf($commandTemplate,
                $this->databaseCredentials['source']['username'],
                $this->databaseCredentials['source']['hostname'],
                $this->databaseCredentials['source']['password'],
                $this->databaseCredentials['source']['database'],
                $this->databaseCredentials['destination']['username'],
                $this->databaseCredentials['destination']['hostname'],
                $this->databaseCredentials['destination']['password'],
                $this->databaseCredentials['destination']['database']));
    }

    /**
     * @param $dataSet IDataSet
     * @return IDataSet
     */
    public function prepareDataSetWithDataReplacement($dataSet)
    {
        return $this->replacement->replaceMarkers($dataSet);
    }

    /**
     * @param AbstractDataSet $dataSet
     * @param string $databaseName
     * @throws \Exception
     */
    public function insertDataSetIntoDatabase($dataSet, $databaseName)
    {
        $preparedDataSet = $this->prepareDataSetWithDataReplacement($dataSet);
        $operation = Factory::INSERT();
        $operation->execute($this->getConnectionBySchema($databaseName), $preparedDataSet);
    }

    /**
     * @param string $databaseName
     * @param string $tableName
     * @return void
     * @throws \Exception
     */
    public function truncateDatabaseTable($databaseName, $tableName)
    {
        $query = sprintf("TRUNCATE TABLE %s", $tableName);
        $this->getConnectionBySchema($databaseName)->getConnection()->query($query)->execute();
    }
}
