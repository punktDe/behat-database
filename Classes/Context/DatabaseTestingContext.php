<?php
namespace PunktDe\Behat\Database\Context;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Neos\Utility\Files;
use PunktDe\Behat\Database\Utility\ContextSettingsService;
use PunktDe\Behat\Database\DataSet\ArrayDataSet;
use PunktDe\Behat\Database\DatabaseManager;

/**
 * Database Testing context
 *
 * Configuration Example for the Database Testing Context:
 *
 * <code>
 * contexts: &contexts
 *   -
 *     PunktDe\Behat\Database\Context\DatabaseTestingContext:
 *       fixtureBasePath: '%paths.base%/Fixtures'
 *       featureBasePath: '%paths.base%/Tests/Behat/Features'
 *       databaseCredentials:
 *         bud_spencers_database:
 *           hostname: localhost
 *           username: bud
 *           password: spencer
 *         terence_hills_database:
 *           hostname: localhost
 *           username: terence
 *           password: hill
 *       bootstrap:
 *          - PunktDe\Behat\Database\Command\FixtureImporter:
 *              source: bud_spencers_database
 *              destination: terence_hills_database
 * </code>
 */
class DatabaseTestingContext implements Context
{
    /**
     * @var \PunktDe\Behat\Database\DatabaseManager
     */
    protected $databaseManager;

    /**
     * Absolute base path for the Features directory
     *
     * use the `%paths.base%` prefix to get the absolute path in the behat.yaml
     *
     * @var string
     */
    protected $featureBasePath;

    /**
     * Absolute base Path for SQL Fixtures directory
     *
     * use the `%paths.base%` prefix to get the absolute path in the behat.yaml
     *
     * @var string
     */
    protected $fixtureBasePath;

    /**
     * @var string
     */
    protected $domainName;

    /**
     * Holds an array of database dump files, which are already imported into the database
     *
     * @var array
     */
    protected static $importedDatabases = [];

    /**
     * @param array $databaseCredentials
     * @param string $featureBasePath Absolute base path for the Features directory
     * @param string $fixtureBasePath Absolute base Path for SQL Fixtures directory
     * @param array $sqlToBeExecutedForSuite Not relevant for constructor, but required for Behat object instantiation!
     * @param array $bootstrap
     * @param string $domainName
     */
    public function __construct(array $databaseCredentials, $featureBasePath, $fixtureBasePath = null, $sqlToBeExecutedForSuite = null, $bootstrap = null, $domainName = null)
    {
        $this->featureBasePath = realpath($featureBasePath);
        $this->domainName = $domainName;
        $this->databaseManager = new DatabaseManager($databaseCredentials, $domainName);
        $this->fixtureBasePath = realpath($fixtureBasePath);
        if (!is_dir($featureBasePath)) {
            throw new SuiteConfigurationException(sprintf('The basePath %s was not found.', $featureBasePath), 1407857346);
        }
        if (!is_null($fixtureBasePath) && !is_dir($this->fixtureBasePath)) {
            throw new SuiteConfigurationException(sprintf('The fixtureBasePath %s was not found relative to the place where behat.yml is stored.', $this->fixtureBasePath), 1407858346);
        }
    }

    /**
     * Run bootstrap commands
     *
     * - The bootstrap concept exploits behat's BeforeSuite hook, since this is the first hook to be executed.
     * - The command pattern guarantees extensibility.
     *
     * @BeforeSuite
     */
    public static function bootstrap(BeforeSuiteScope $scope)
    {
        $databaseTestingContextSettings = ContextSettingsService::extractClassContextSettingsFromScope($scope, get_called_class());

        if (is_array($databaseTestingContextSettings) && array_key_exists('bootstrap', $databaseTestingContextSettings)) {
            foreach ($databaseTestingContextSettings['bootstrap'] as $settings) {
                $className = key($settings);
                if (class_exists($className)) {
                    $bootstrapCommand = new $className($databaseTestingContextSettings, current($settings)); /** @var \PunktDe\Behat\Database\Command\AbstractCommand $bootstrapCommand */
                    $bootstrapCommand->execute();
                }
            }
        }
    }

    /**
     * @return DatabaseManager
     */
    public function getDatabaseManager()
    {
        return $this->databaseManager;
    }

    /**
     * @Given /^dataset "([^"]*)" is imported to "([^"]*)"$/
     */
    public function importDataSetToDatabase($dataSetFilePath, $schema)
    {
        $dataSetFilePath = $this->prepareFixturePath($dataSetFilePath);
        $this->databaseManager->importDataSetToDatabase($dataSetFilePath, $schema);
    }

    /**
     * @Given /^dataset "([^"]*)" is added to "([^"]*)"$/
     * @Given die Datensätze aus :dataSetFilePath wurden zur :schema hinzugefügt
     */
    public function addDataSetToDatabase($dataSetFilePath, $schema)
    {
        $dataSetFilePath = $this->prepareFixturePath($dataSetFilePath);
        $this->databaseManager->addDataSetToDatabase($dataSetFilePath, $schema);
    }

    /**
     * @Given /^the database dump "([^"]*)" is imported to "([^"]*)" once$/
     */
    public function importDatabaseDumpToDatabaseOnce($dumpFilePath, $schema = null)
    {
        $fullDumpFilePath = $this->prepareFixturePath($dumpFilePath);
        if (!in_array($fullDumpFilePath, self::$importedDatabases)) {
            $this->importDatabaseDumpToDatabase($dumpFilePath, $schema);
        } else {
            echo "Database dump $fullDumpFilePath is NOT imported into $schema since it's already been imported!";
        }
    }

    /**
     * @Given /^the database dump "([^"]*)" is imported to "([^"]*)"$/
     */
    public function importDatabaseDumpToDatabase($dumpFilePath, $schema = null)
    {
        $fullDumpFilePath = $this->prepareFixturePath($dumpFilePath);
        echo "Database dump $fullDumpFilePath is imported into $schema";
        $this->databaseManager->importDumpToDatabase($fullDumpFilePath, $schema);
        if (!in_array($fullDumpFilePath, self::$importedDatabases)) {
            self::$importedDatabases[] = $fullDumpFilePath;
        }
    }

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        self::importSqlDumpsForTestSuite($scope);
    }

    /**
     * Imports an sql dump into given databases BEFORE the suite is run.
     *
     * The configuration for the dumps is taken from the behat.yaml context configuration.
     *
     * Configuration Example:
     *
     * <code>
     * contexts: &contexts
     *   -
     *     PunktDe\Behat\Database\Context\DatabaseTestingContext:
     *       featureBasePath: %paths.base%/Tests/Behat/Features
     *       databaseCredentials:
     *         onebruker_test:
     *           hostname: localhost
     *           username: onebruker_test
     *           password: onebruker_test
     *       sqlToBeExecutedForSuite:
     *         -
     *           onebruker_test:
     *             -
     *               Fixtures/test_dump.sql
     * </code>
     *
     *
     * @param BeforeSuiteScope $scope
     */
    protected static function importSqlDumpsForTestSuite(BeforeSuiteScope $scope)
    {
        $databaseTestingContextSettings = ContextSettingsService::extractClassContextSettingsFromScope($scope, get_called_class());

        $featureBasePath = $databaseTestingContextSettings['featureBasePath'];

        if (is_array($databaseTestingContextSettings) && array_key_exists('sqlToBeExecutedForSuite', $databaseTestingContextSettings)) {
            $databaseManager = new DatabaseManager($databaseTestingContextSettings['databaseCredentials']);

            foreach ($databaseTestingContextSettings['sqlToBeExecutedForSuite'] as $databaseImports) {
                foreach ($databaseImports as $databaseName => $dumpFiles) {
                    foreach ($dumpFiles as $dumpFile) {
                        echo "Importing $dumpFile into $databaseName\n";
                        $databaseManager->importDumpToDatabase(Files::concatenatePaths([$featureBasePath, $dumpFile]), $databaseName);
                    }
                }
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function cleanUp()
    {
        echo "Clean up database connections";
        $this->databaseManager->cleanConnections();
    }

    /**
     * usage:
     *	the tricky thing is, that the PyStringNode $queryString is magically mixed in.
     * 	The $queryString is passed to the function by simply adding the string in tripple double quotes at the next line.
     *
     * Example:
     * 	Then The given query on database "MyDatabase" should match expected table "MyTable" of dataset "path/to/a.yaml"
     * 	"""
     * 	SELECT * FROM MyDatabase WHERE someStrangeField = "evenStranger"
     * 	""" <----- triple double quotes (PyString)
     *
     * @Then /^the given query on database "(?P<schema>[^"]+)" should match expected table "(?P<table>[^"]+)" of dataset "(?P<expectedDataSetFile>[^"]+)"$/
     * @Then /^the expected dataset "(?P<expectedDataSetFile>[^"]+)" table "(?P<table>[^"]+)" equals database "(?P<schema>[^"]+)" sql dataset$/
     */
    public function assertExpectedFixtureEquals($schema, $table, $expectedDataSetFile, PyStringNode $queryString)
    {
        $expectedDataSetFile = $this->prepareFixturePath($expectedDataSetFile);
        if (!file_exists($expectedDataSetFile) || !is_readable($expectedDataSetFile)) {
            throw new SuiteConfigurationException(sprintf('The given Dataset %s was not found.', $expectedDataSetFile), 1408034776);
        }
        $expectedDataSet = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet($expectedDataSetFile);
        $oneLineQueryString = trim(preg_replace('/\s+/', ' ', (string) $queryString));
        $query = $this->databaseManager->getConnectionBySchema($schema)->createQueryTable($table, $oneLineQueryString);
        \PHPUnit_Extensions_Database_TestCase::assertTablesEqual($expectedDataSet->getTable($table), $query);
    }

    /**
     * @Then the database query :query on database :database should return
     */
    public function theDatabaseQueryShouldBe($query, $database, TableNode $expected)
    {
        $result = $this->databaseManager->getConnectionBySchema($database)->createQueryTable('table', $query);
        $expectedData = array_values($expected->getTable());

        $expectedDataSet = $this->databaseManager->prepareDataSetWithDataReplacement(new ArrayDataSet(['table' => $expectedData]));

        \PHPUnit_Extensions_Database_TestCase::assertTablesEqual($expectedDataSet->getTable('table'), $result);
    }

    /**
     * @Then the database query :query is executed on database :database
     */
    public function executeTheGivenQuery($query, $database)
    {
        $dbConnection = $this->databaseManager->getConnectionBySchema($database);
        $preparedQuery = $dbConnection->getConnection()->query($query);
        $preparedQuery->execute();
    }

    /**
     * @When records are added to table :tableName of database :databaseName:
     */
    public function addRecordsToTable($tableName, $databaseName, TableNode $tableNode)
    {
        $table = array_values($tableNode->getTable());
        $dataSet = $this->databaseManager->prepareDataSetWithDataReplacement(new ArrayDataSet([$tableName => $table]));
        $this->databaseManager->insertDataSetIntoDatabase($dataSet, $databaseName);
    }

    /**
     * @When table :tableName of database :databaseName is truncated
     */
    public function truncateTable($tableName, $databaseName)
    {
        $this->databaseManager->truncateDatabaseTable($databaseName, $tableName);
    }

    /**
     * @param $path
     * @return string
     */
    protected function prepareFixturePath($path)
    {
        if ($this->fixtureBasePath) {
            $fixturePath = Files::concatenatePaths([$this->fixtureBasePath, $path]);
            if (file_exists($fixturePath)) {
                return $fixturePath;
            }
        }
        return Files::concatenatePaths([$this->featureBasePath, $path]);
    }
}
