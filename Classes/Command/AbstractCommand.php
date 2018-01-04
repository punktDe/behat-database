<?php
namespace PunktDe\Behat\Database\Command;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

abstract class AbstractCommand
{
    /**
     * @var array
     */
    protected $contextSettings = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @param array $contextSettings
     * @param array $settings
     */
    public function __construct(array $contextSettings, array $settings)
    {
        $this->contextSettings = $contextSettings;
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    abstract public function execute();
}
