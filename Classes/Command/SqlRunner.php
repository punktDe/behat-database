<?php
namespace PunktDe\Behat\Database\Command;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Neos\Utility\Files;

class SqlRunner extends SqlCommand
{
    /**
     * @var array
     */
    protected $databaseReferences = array(
        'database',
    );

    /**
     * @return string
     */
    protected function runCommand()
    {
        $this->databaseManager->importDumpToDatabase(realpath(Files::concatenatePaths(array($this->contextSettings['featureBasePath'], $this->settings['file']))));
    }
}
