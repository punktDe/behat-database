<?php
namespace PunktDe\Behat\Database\Utility;

/*
 *  (c) 2017 punkt.de GmbH - Karlsruhe, Germany - http://punkt.de
 *  All rights reserved.
 */

use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ReplacementDataSet;

class Replacement
{
    /**
     * @var string
     */
    protected $domainName = '';

    /**
     * @var mixed
     */
    protected $subject;

    /**
     * @param string $domain
     */
    public function __construct($domain = '')
    {
        $this->domainName = $domain;
    }

    /**
     * @return array
     */
    public function getMarkerReplacements()
    {
        return array(
            '[[[CURRENT_DAY]]]' => date('Ymd'),
            '[[[CURRENT_DAY_TIMESTAMP]]]' => time(),
            '[[[CURRENT_DAY_MIDNIGHT_TIMESTAMP]]]' => strtotime('midnight'),
            '[[[TOMORROW]]]' => strtotime('tomorrow'),
            '[[[NEXT_DAY_TIMESTAMP]]]' => time() + 86400,
            '[[[PREVIOUS_DAY_TIMESTAMP]]]' => time() - 86400,
            '[[[DOMAIN_NAME]]]' => $this->domainName,
            '[[[PREVIOUS_MONTH_TIMESTAMP]]]' => strtotime('-1 month midnight'),
            '[[[IN_FOUR_WEEKS_TIMESTAMP]]]' => time() + (86400 * 28),
            '[[[IN_TEN_WEEKS_TIMESTAMP]]]' => time() + (86400 * 70)
        );
    }

    /**
     * @param string $subject
     * @return mixed
     */
    public function replaceMarkers($subject)
    {
        $this->subject = $subject;
        if ($subject instanceof IDataSet) {
            return $this->removeMarkersFromDataSet();
        }
        if (is_string($subject)) {
            return $this->removeMarkersFromString();
        }
        return $subject;
    }

    /**
     * @return IDataSet
     */
    protected function removeMarkersFromDataSet()
    {
        return new ReplacementDataSet($this->subject, $this->getMarkerReplacements());
    }

    /**
     * @return string
     */
    protected function removeMarkersFromString()
    {
        return str_replace(array_keys($this->getMarkerReplacements()), array_values($this->getMarkerReplacements()), $this->subject);
    }
}
