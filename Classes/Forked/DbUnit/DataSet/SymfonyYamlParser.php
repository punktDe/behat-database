<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

use Symfony;

/**
 * The default YAML parser, using Symfony/Yaml.
 */
class SymfonyYamlParser implements IYamlParser
{
    public function parseYaml($yamlFile)
    {
        return Symfony\Component\Yaml\Yaml::parse(\file_get_contents($yamlFile));
    }
}
