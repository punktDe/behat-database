<?php
namespace PunktDe\Behat\Database\Utility;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use Behat\Testwork\Hook\Scope\HookScope;

/**
 * Context Settings Service
 *
 * TODO: Convert this class into a trait to remove parameter $contextClass from method extractContextSettingsFromScope()
 */
class ContextSettingsService
{
    /**
     * @param \Behat\Testwork\Hook\Scope\HookScope $scope
     * @param string $className
     * @return array
     */
    static public function extractClassContextSettingsFromScope(HookScope $scope, $className)
    {
        $contextClassSettings = array();
        $contextsSettings = $scope->getEnvironment()->getSuite()->getSetting('contexts');
        $databaseTestingContextSettings = null;
        foreach ($contextsSettings as $contextSettings) {
            if (is_array($contextSettings) && array_key_exists($className, $contextSettings)) {
                $contextClassSettings = $contextSettings[$className];
            }
        }
        return $contextClassSettings;
    }
}
