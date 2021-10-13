<?php
namespace PunktDe\Testing\Forked\DbUnit\Constraint;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use PunktDe\Testing\Forked\DbUnit\DataSet\ITable;
use PunktDe\Testing\Forked\DbUnit\Exception\InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts whether or not two dbunit tables are equal.
 */
class TableIsEqual extends Constraint
{
    /**
     * @var ITable
     */
    protected $value;

    /**
     * @var string
     */
    protected $failure_reason;

    /**
     * Creates a new constraint.
     *
     * @param ITable $value
     */
    public function __construct(ITable $value)
    {
        $this->value = $value;
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
        if (!$other instanceof ITable) {
            throw new InvalidArgumentException(
                'PHPUnit_Extensions_Database_DataSet_ITable expected'
            );
        }

        return $this->value->matches($other);
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other Evaluated value or object.
     *
     * @return string
     */
    protected function failureDescription($other): string
    {
        return $other->__toString() . ' ' . $this->toString();
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString(): string
    {
        return \sprintf(
            'is equal to expected %s', $this->value->__toString()
        );
    }
}
