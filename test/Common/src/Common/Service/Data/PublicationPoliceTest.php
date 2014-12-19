<?php

namespace OlcsTest\Service\Data;

use Common\Service\Data\PublicationPolice as PoliceDataService;

/**
 * Class PublicationPoliceTest
 * @package OlcsTest\Service\Data
 */
class PublicationPoliceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests creating an empty object
     */
    public function testCreateEmpty()
    {
        $sut = new PoliceDataService();
        $this->assertEquals('Common\Data\Object\PublicationPolice', get_class($sut->createEmpty()));
    }
}
