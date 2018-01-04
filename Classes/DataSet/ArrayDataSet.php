<?php
namespace PunktDe\Behat\Database\DataSet;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

/**
 * ArrayDataSet
 *
 * Code is based on example in PHPUnit documentation
 *
 * @see http://phpunit.de/manual/3.7/en/database.html
 */
class ArrayDataSet extends \PHPUnit_Extensions_Database_DataSet_AbstractDataSet
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

            $metaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
            $table = new \PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

            foreach ($rows as $row) {
                $row = array_combine($columns, $row);
                $table->addRow($row);
            }
            $this->tables[$tableName] = $table;
        }
    }

    /**
     * @param bool $reverse
     * @return \PHPUnit_Extensions_Database_DataSet_DefaultTableIterator|\PHPUnit_Extensions_Database_DataSet_ITableIterator
     */
    protected function createIterator($reverse = false)
    {
        return new \PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    /**
     * @param string $tableName
     * @return \PHPUnit_Extensions_Database_DataSet_ITable
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
