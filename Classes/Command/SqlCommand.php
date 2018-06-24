<?php
namespace PunktDe\Behat\Database\Command;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use PunktDe\Behat\Database\DatabaseManager;

abstract class SqlCommand extends AbstractCommand
{
    /**
     * @var array
     */
    protected $credentials = array();

    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var array
     */
    protected $databaseReferences = array();

    /**
     * @return void
     * @throws \Exception
     */
    public function execute()
    {
        $this->prepareCredentials();
        $this->prepareDatabaseManager();
        $this->runCommand();
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function prepareCredentials()
    {
        foreach ($this->databaseReferences as $databaseReference) {
            $this->setCredentialsByParticipant($databaseReference);
        }
    }

    /**
     * @return void
     */
    protected function prepareDatabaseManager()
    {
        $this->databaseManager = new DatabaseManager($this->credentials);
    }

    /**
     * @param string $participant
     * @throws \Exception
     * @return void
     */
    protected function setCredentialsByParticipant($participant)
    {
        if (!(array_key_exists('databaseCredentials', $this->contextSettings) && array_key_exists($this->settings[$participant], $this->contextSettings['databaseCredentials']))) {
            throw new \Exception('No database credentials found for database ' . $this->settings[$participant], 1409736237);
        }

        $participantCredentials = $this->contextSettings['databaseCredentials'][$this->settings[$participant]];
        $this->credentials[$participant]['username'] = $participantCredentials['username'];
        $this->credentials[$participant]['password'] = $participantCredentials['password'];
        $this->credentials[$participant]['hostname'] = $participantCredentials['hostname'];
        $this->credentials[$participant]['database'] = array_key_exists('database', $participantCredentials) ? $participantCredentials['database'] : $this->settings[$participant];
    }

    /**
     * @return string
     */
    abstract protected function runCommand();
}
