<?php

/**
 * Licence service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Licence;

use Common\Service\Licence\Licence;
use OlcsTest\Bootstrap;

/**
 * Licence service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Licence service
     */
    public $licence;

    /**
     * Licence number
     */
    public $licNo;

    /**
     * TrafficArea
     */
    public $trafficArea;

    /**
     * Goods or psv
     */
    public $goodsOrpsv;

    /**
     * Set up the liccence service
     */
    public function setUp()
    {
        $this->licence = $this->getMock('Common\Service\Licence\Licence', array('makeRestCall'));

        $this->licence->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCalls')));
    }

    /**
     * Test new licence generation
     * 
     * @dataProvider licenceDetailsProvider
     */
    public function testGenerateLicence($trafficArea, $licNo, $goodsOrPsv, $expectedLicence)
    {
        $this->trafficArea = $trafficArea;
        $this->licNo = $licNo;
        $this->goodsOrPsv = $goodsOrPsv;

        $newLicence = $this->licence->generateLicence(1);
        $this->assertEquals($newLicence, $expectedLicence);
    }

    /**
     * Provider for generateLicence
     *
     * @return array
     */
    public function licenceDetailsProvider()
    {
        return array(
            // new PSV licence
            array('K', null, Licence::GOODS_OR_PSV_PSV, 'PK1'),
            // new Goods licence
            array('K', null, Licence::GOODS_OR_PSV_GOODS_VEHICLE, 'OK1'),
            // no traffic area - no licence generated
            array(null, null, Licence::GOODS_OR_PSV_PSV, ''),
            // existing PSV licence - changing traffic area
            array('K', 'PB1', Licence::GOODS_OR_PSV_PSV, 'PK1'),
            // existing Goods licence - changing traffic area
            array('K', 'OB1', Licence::GOODS_OR_PSV_PSV, 'OK1'),
            // existing PSV licence - no traffic area - no licence generated
            array(null, 'OB1', Licence::GOODS_OR_PSV_PSV, ''),
            // wrong PSV / GOODS code - no licence generated
            array('K', null, 'A', ''),
            // empty PSV / GOODS code - no licence generated
            array('K', null, '', ''),
        );
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     */
    public function mockRestCalls($service, $method, $data = array(), $bundle = array())
    {
        $applicationBundle = array(
            'properties' => null,
            'children' => array(
                'licence' => array(
                    'properties' => array(
                        'id',
                        'licNo',
                        'version'
                    ),
                    'children' => array(
                        'trafficArea' => array(
                            'properties' => array(
                                'id',
                            )
                        ),
                        'goodsOrPsv' => array(
                            'properties' => array(
                                'id'
                            )
                        )
                    )
                )
            )
        );
        if ($service == 'Application' && $method == 'GET' && $bundle == $applicationBundle) {
            if ($this->trafficArea) {
                return array(
                    'licence' => array(
                        'id' => 1,
                        'licNo' => $this->licNo,
                        'version' => 1,
                        'trafficArea' => array(
                            'id' => $this->trafficArea
                        ),
                        'goodsOrPsv' => array(
                            'id' => $this->goodsOrPsv
                        )
                    ),
                );
            }
        }
        if ($service == 'LicenceNoGen' && $method == 'POST') {
            return array(
                'id' => 1
            );
        }
        if ($service == 'Licence' && $method == 'PUT') {
            return array();
        }
    }

    /**
     * Test setServiceLocator
     *
     * @dataProvider providerSetServiceLocator
     */
    public function testServiceLocator($input, $output)
    {
        $this->licence->setServiceLocator($input);
        $this->assertInstanceOf(get_class($output), $this->licence->getServiceLocator());
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
