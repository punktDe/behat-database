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

use PunktDe\Behat\Database\Forked\DbUnit\DataSet\IDataSet;

/**
 * Provides an interface for creating data sets from data set spec strings.
 */
interface Specification
{
    /**
     * Creates a data set from a data set spec string.
     *
     * @param string $dataSetSpec
     *
     * @return IDataSet
     */
    public function getDataSet($dataSetSpec);
}
