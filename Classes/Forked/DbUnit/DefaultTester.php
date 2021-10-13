<?php
namespace PunktDe\Testing\Forked\DbUnit;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Database\Connection;

/**
 * This is the default implementation of the database tester. It receives its
 * connection object from the constructor.
 */
class DefaultTester extends AbstractTester
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Creates a new default database tester using the given connection.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->connection = $connection;
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
