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

use PunktDe\Behat\Database\Forked\DbUnit\DataSet\FlatXmlDataSet;

/**
 * Creates a FlatXML dataset based off of a spec string.
 *
 * The format of the spec string is as follows:
 *
 * <filename>
 *
 * The filename should be the location of a flat xml file relative to the
 * current working directory.
 */
class FlatXml implements Specification
{
    /**
     * Creates Flat XML Data Set from a data set spec.
     *
     * @param string $dataSetSpec
     *
     * @return FlatXmlDataSet
     */
    public function getDataSet($dataSetSpec)
    {
        return new FlatXmlDataSet($dataSetSpec);
    }
}
