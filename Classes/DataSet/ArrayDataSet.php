<?php
namespace PunktDe\Behat\Database\DataSet;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use PunktDe\Behat\Database\Forked\DbUnit\DataSet;

/**
 * ArrayDataSet
 *
 * Code is based on example in PHPUnit documentation
 *
 * @see http://phpunit.de/manual/3.7/en/database.html
 */
class ArrayDataSet extends DataSet\AbstractDataSet
{
    /**
     * @var array
     */
    protected $tables = array();

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $tableName => $rows) {
            $columns = array_shift($rows);

            $metaData = new DataSet\DefaultTableMetaData($tableName, $columns);
            $table = new DataSet\DefaultTable($metaData);

            foreach ($rows as $row) {
                $row = array_combine($columns, $row);
                $table->addRow($row);
            }
            $this->tables[$tableName] = $table;
        }
    }

    /**
     * @param bool $reverse
     * @return DataSet\DefaultTableIterator|DataSet\ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new DataSet\DefaultTableIterator($this->tables, $reverse);
    }

    /**
     * @param string $tableName
     * @return DataSet\ITable
     * @throws \InvalidArgumentException
     */
    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new \InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }
}
