<?php
namespace PunktDe\Testing\Forked\DbUnit\Operation;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\Database\Connection;
use PunktDe\Testing\Forked\DbUnit\DataSet\IDataSet;
use PunktDe\Testing\Forked\DbUnit\Exception\InvalidArgumentException;

/**
 * This class facilitates combining database operations. To create a composite
 * operation pass an array of classes that implement
 * PHPUnit_Extensions_Database_Operation_IDatabaseOperation and they will be
 * executed in that order against all data sets.
 */
class Composite implements Operation
{
    /**
     * @var array
     */
    protected $operations = [];

    /**
     * Creates a composite operation.
     *
     * @param array $operations
     */
    public function __construct(array $operations)
    {
        foreach ($operations as $operation) {
            if ($operation instanceof Operation) {
                $this->operations[] = $operation;
            } else {
                throw new InvalidArgumentException('Only database operation instances can be passed to a composite database operation.');
            }
        }
    }

    public function execute(Connection $connection, IDataSet $dataSet)
    {
        try {
            foreach ($this->operations as $operation) {
                /* @var $operation Operation */
                $operation->execute($connection, $dataSet);
            }
        } catch (Exception $e) {
            throw new Exception("COMPOSITE[{$e->getOperation()}]", $e->getQuery(), $e->getArgs(), $e->getTable(), $e->getError());
        }
    }
}
