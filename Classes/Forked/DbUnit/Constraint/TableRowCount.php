<?php
namespace PunktDe\Testing\Forked\DbUnit\Constraint;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts the row count in a table
 */
class TableRowCount extends Constraint
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new constraint.
     *
     * @param $tableName
     * @param $value
     */
    public function __construct($tableName, $value)
    {
        $this->tableName = $tableName;
        $this->value     = $value;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        return $other == $this->value;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString(): string
    {
        return \sprintf('is equal to expected row count %d', $this->value);
    }
}
