<?php

/**
 * TrafficArea Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service;

use CommonTest\Bootstrap;
use Common\Controller\Service\TrafficAreaSectionService;

/**
 * TrafficArea Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TrafficAreaSectionServiceTest extends AbstractSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\TrafficAreaSectionService
     */
    private $sut;

    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new TrafficAreaSectionService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * @group section_service
     * @group traffic_area_section_service
     */
    public function testSetTrafficArea()
    {
        $this->attachRestHelperMock();

        $id = 3;
        $ta = 'B';
        $this->sut->setIdentifier($id);
        $response = array(
            'licence' => array(
                'id' => 7,
                'version' => 1
            )
        );
        $expectedData = array(
            'id' => 7,
            'version' => 1,
            'trafficArea' => $ta
        );

        $this->mockRestHelper->expects($this->at(0))
            ->method('makeRestCall')
            ->with('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->mockRestHelper->expects($this->at(1))
            ->method('makeRestCall')
            ->with('Licence', 'PUT', $expectedData)
            ->will($this->returnValue($response));

        $mockLicence = $this->getMock('\stdClass', array('generateLicence'));
        $mockLicence->expects($this->once())
            ->method('generateLicence')
            ->with($id);

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('licence', $mockLicence);

        $this->sut->setTrafficArea($ta);
    }

    /**
     * @group section_service
     * @group traffic_area_section_service
     */
    public function testSetTrafficAreaToNull()
    {
        $this->attachRestHelperMock();

        $id = 3;
        $ta = null;
        $this->sut->setIdentifier($id);
        $response = array(
            'licence' => array(
                'id' => 7,
                'version' => 1
            )
        );
        $expectedData = array(
            'id' => 7,
            'version' => 1,
            'trafficArea' => $ta
        );

        $this->mockRestHelper->expects($this->at(0))
            ->method('makeRestCall')
            ->with('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->mockRestHelper->expects($this->at(1))
            ->method('makeRestCall')
            ->with('Licence', 'PUT', $expectedData)
            ->will($this->returnValue($response));

        // This is to assert that licence doesn't get generated
        $mockLicence = $this->getMock('\stdClass', array('generateLicence'));
        $mockLicence->expects($this->never())
            ->method('generateLicence')
            ->with($id);

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('licence', $mockLicence);

        $this->sut->setTrafficArea($ta);
    }

    /**
     * @group section_service
     * @group traffic_area_section_service
     */
    public function testGetTrafficArea()
    {
        $this->attachRestHelperMock();

        $id = 3;
        $this->sut->setIdentifier($id);
        $response = array(
            'licence' => array(
                'trafficArea' => 'foo'
            )
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Application', 'GET', $id)
            ->will($this->returnValue($response));


        $this->assertEquals('foo', $this->sut->getTrafficArea());
    }

    /**
     * @group section_service
     * @group traffic_area_section_service
     */
    public function testGetTrafficAreaWithNoResults()
    {
        $this->attachRestHelperMock();

        $id = 3;
        $this->sut->setIdentifier($id);
        $response = false;

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Application', 'GET', $id)
            ->will($this->returnValue($response));


        $this->assertEquals(null, $this->sut->getTrafficArea());
    }

    /**
     * @group section_service
     * @group traffic_area_section_service
     */
    public function testGetTrafficAreaValueOptions()
    {
        $this->attachRestHelperMock();

        $response = array(
            'Count' => 3,
            'Results' => array(
                array(
                    'id' => 'A',
                    'name' => 'Foo'
                ),
                array(
                    'id' => 'B',
                    'name' => 'Bar'
                ),
                array(
                    'id' => 'C',
                    'name' => 'Cake'
                ),
                array(
                    'id' => 'N',
                    'name' => 'Northern Ireland'
                )
            )
        );

        $expected = array(
            'B' => 'Bar',
            'C' => 'Cake',
            'A' => 'Foo'
        );

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('TrafficArea', 'GET', array())
            ->will($this->returnValue($response));


        $options = $this->sut->getTrafficAreaValueOptions();

        $this->assertEquals($expected, $options);
    }
}
