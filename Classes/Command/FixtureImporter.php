<?php
namespace PunktDe\Behat\Database\Command;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

class FixtureImporter extends SqlCommand
{
    /**
     * @var array
     */
    protected $databaseReferences = array(
        'source',
        'destination'
    );

    /**
     * @return string
     */
    protected function runCommand()
    {
        $this->databaseManager->copyDatabase();
    }
}
