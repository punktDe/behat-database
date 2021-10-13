<?php
namespace PunktDe\Testing\Forked\DbUnit;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Constraint\TableIsEqual;
use PunktDe\Testing\Forked\DbUnit\Constraint\TableRowCount;
use PunktDe\Testing\Forked\DbUnit\Database\Connection;
use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;

/**
 * A TestCase extension that provides functionality for testing and asserting
 * against a real database.
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    abstract protected function getConnection();

    /**
     * Asserts that two given tables are equal.
     *
     * @param ITable $expected
     * @param ITable $actual
     * @param string $message
     */
    public static function assertTablesEqual(ITable $expected, ITable $actual, $message = '')
    {
        $constraint = new TableIsEqual($expected);

        self::assertThat($actual, $constraint, $message);
    }

    /**
     * Assert that a given table has a given amount of rows
     *
     * @param string $tableName Name of the table
     * @param int    $expected  Expected amount of rows in the table
     * @param string $message   Optional message
     */
    public function assertTableRowCount($tableName, $expected, $message = '')
    {
        $constraint = new TableRowCount($tableName, $expected);
        $actual     = $this->getConnection()->getRowCount($tableName);

        self::assertThat($actual, $constraint, $message);
    }
}
