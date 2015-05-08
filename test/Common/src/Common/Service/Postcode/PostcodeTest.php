<?php

/**
 * Postcode service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Postcode;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Postcode\Postcode;
use CommonTest\Bootstrap;

/**
 * Postcode service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PostcodeTest extends MockeryTestCase
{

    /**
     * Postocde service
     */
    public $postcode;

    /**
     * Set up the postocde service
     */
    public function setUp()
    {
        $this->postcode = $this->getMock('Common\Service\Postcode\Postcode', array('sendGet', 'makeRestCall'));

        $this->postcode->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCalls')));
    }

    /**
     * Test postcode with correct postcode
     * 
     * @dataProvider postcodeProvider
     * @group postcodeService
     */
    public function testPostcodeWithCorrectPostcode($response)
    {

        $this->postcode->expects($this->any())
            ->method('sendGet')
            ->will($this->returnValue($response));

        $result = $this->postcode->getTrafficAreaByPostcode('LS1 4ES');

        $this->assertEquals(is_array($result), true);
        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0], 'B');
        $this->assertEquals($result[1], 'North East of England');
    }

    /**
     * Postcode provider
     * 
     */
    public function postcodeProvider()
    {
        return [
            [
                [
                    'administritive_area' => 'LEEDS'
                ]
            ],
            [
                [
                    [
                        'administritive_area' => 'LEEDS'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test postcode with wrong postcode
     */
    public function testPostcodeWithWrongPostcode()
    {
        $this->postcode->expects($this->any())
            ->method('sendGet')
            ->will($this->returnValue(array()));

        $result = $this->postcode->getTrafficAreaByPostcode('WRONGCODE');

        $this->assertEquals(is_array($result), true);
        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0], null);
        $this->assertEquals($result[1], null);
    }

    public function testGetEnforcementAreaByPostcodePrefixAndDigit()
    {
        $postcode = 'LS9 6NF';

        $sm = Bootstrap::getServiceManager();
        $this->postcode->setServiceLocator($sm);
        $mockEntityService = m::mock();

        $sm->shouldReceive('get')->with('Entity\PostcodeEnforcementArea')
            ->andReturn($mockEntityService);

        $mockEntityService
            ->shouldReceive('getEnforcementAreaByPostcodePrefix')
            ->with('LS9 6')
            ->once()
            ->andReturn('ENFORCEMENT_AREA');

        $this->assertEquals(
            'ENFORCEMENT_AREA',
            $this->postcode->getEnforcementAreaByPostcode($postcode)
        );
    }

    public function testGetEnforcementAreaByPostcodePrefix()
    {
        $postcode = 'LS9 6NF';

        $sm = Bootstrap::getServiceManager();
        $this->postcode->setServiceLocator($sm);
        $mockEntityService = m::mock();

        $sm->shouldReceive('get')->with('Entity\PostcodeEnforcementArea')
            ->andReturn($mockEntityService);

        $mockEntityService
            ->shouldReceive('getEnforcementAreaByPostcodePrefix')
            ->with('LS9 6')
            ->once()
            ->andReturn(null)
            ->shouldReceive('getEnforcementAreaByPostcodePrefix')
            ->with('LS9')
            ->once()
            ->andReturn('ENFORCEMENT_AREA');

        $this->assertEquals(
            'ENFORCEMENT_AREA',
            $this->postcode->getEnforcementAreaByPostcode($postcode)
        );
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     */
    public function mockRestCalls($service, $method, $data = array())
    {
        if ($service == 'AdminAreaTrafficArea' && $method == 'GET') {

            if (is_array($data) && $data['id'] == 'LEEDS') {
                return array(
                        'trafficArea' => array(
                            'id' => 'B',
                            'name' => 'North East of England'
                        )
                );
            } else {
                return array();
            }
        }
    }

    /**
     * Test setServiceLocator
     *
     * @dataProvider providerSetServiceLocator
     */
    public function testServiceLocator($input, $output)
    {
        $this->postcode->setServiceLocator($input);
        $this->assertInstanceOf(get_class($output), $this->postcode->getServiceLocator());
    }

    /**
     * Provider for setServiceLocator
     */
    public function providerSetServiceLocator()
    {
        $serviceManager = Bootstrap::getServiceManager();
        return array(
            array($serviceManager, $serviceManager)
        );
    }
}
