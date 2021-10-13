<?php
namespace PunktDe\Testing\Forked\DbUnit\Operation;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;
use PunktDe\Testing\Forked\DbUnit\Exception\RuntimeException;

/**
 * Thrown for exceptions encountered with database operations. Provides
 * information regarding which operations failed and the query (if any) it
 * failed on.
 */
class Exception extends RuntimeException
{
    /**
     * @var string
     */
    protected $operation;

    /**
     * @var string
     */
    protected $preparedQuery;

    /**
     * @var array
     */
    protected $preparedArgs;

    /**
     * @var ITable
     */
    protected $table;

    /**
     * @var string
     */
    protected $error;

    /**
     * Creates a new dbunit operation exception
     *
     * @param string $operation
     * @param string $current_query
     * @param ITable $current_table
     * @param string $error
     */
    public function __construct($operation, $current_query, $current_args, $current_table, $error)
    {
        parent::__construct("{$operation} operation failed on query: {$current_query} using args: " . \print_r($current_args, true) . " [{$error}]");

        $this->operation     = $operation;
        $this->preparedQuery = $current_query;
        $this->preparedArgs  = $current_args;
        $this->table         = $current_table;
        $this->error         = $error;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getQuery()
    {
        return $this->preparedQuery;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getArgs()
    {
        return $this->preparedArgs;
    }

    public function getError()
    {
        return $this->error;
    }
}
