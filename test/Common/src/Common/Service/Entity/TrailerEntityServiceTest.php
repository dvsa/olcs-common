<?php

/**
 * TrailerEntityServiceTest.php
 */
namespace CommonTest\Service\Entity;

use Mockery as m;

use Common\Service\Entity\TrailerEntityService;

/**
 * Trailer entity service tests.
 *
 * @author Josh Curtis <josh.curtis@valtech.com>
 */
class TrailerEntityServiceTest extends AbstractEntityServiceTestCase
{
    /**
     * Set up the entity service test.
     */
    protected function setUp()
    {
        $this->sut = new TrailerEntityService();

        parent::setUp();
    }

    /**
     * Test the get trailer by licence function behaves as expected.
     */
    public function testGetTrailerDataForLicence()
    {
        $licenceId = 1;

        $response = array();

        $this->expectOneRestCall('Trailer', 'GET', ['licence' => $licenceId])
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->getTrailerDataForLicence($licenceId));
    }

    public function testGetTrailerThrowsInvalidArgumentExection()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->getTrailerDataForLicence(null);
    }
}
