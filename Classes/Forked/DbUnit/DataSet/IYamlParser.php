<?php
namespace PunktDe\Testing\Forked\DbUnit\DataSet;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 *
 *  based on DbUnit by Sebastian Bergmann
 */

/**
 * An interface for parsing YAML files.
 */
interface IYamlParser
{
    /**
     * @param string $yamlFile
     *
     * @return array parsed YAML
     */
    public function parseYaml($yamlFile);
}
