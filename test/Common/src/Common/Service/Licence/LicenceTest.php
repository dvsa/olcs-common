<?php

/**
 * Licence service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Licence;

use Common\Service\Licence\Licence;

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
    public $goodsOrPsv;

    /**
     * Flag for testing failure of licence generation
     */
    public $shouldFail = false;

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
            array('K', null, Licence::LICENCE_CATEGORY_PSV, 'PK1'),
            // new Goods licence
            array('K', null, Licence::LICENCE_CATEGORY_GOODS_VEHICLE, 'OK1'),
            // no traffic area - no licence generated
            array(null, null, Licence::LICENCE_CATEGORY_PSV, ''),
            // existing PSV licence - changing traffic area
            array('K', 'PB1', Licence::LICENCE_CATEGORY_PSV, 'PK1'),
            // existing Goods licence - changing traffic area
            array('K', 'OB1', Licence::LICENCE_CATEGORY_PSV, 'OK1'),
            // existing PSV licence - no traffic area - no licence generated
            array(null, 'OB1', Licence::LICENCE_CATEGORY_PSV, ''),
            // wrong PSV / GOODS code - no licence generated
            array('K', null, 'A', ''),
            // empty PSV / GOODS code - no licence generated
            array('K', null, '', ''),
        );
    }

    /**
     * Test licence generation failure
     */
    public function testGenerateLicenceFailure()
    {
        $this->trafficArea = 'B';
        $this->licNo = null;
        $this->goodsOrPsv = Licence::LICENCE_CATEGORY_PSV;
        $this->shouldFail = true;

        $this->setExpectedException('\Exception');

        $this->licence->generateLicence(1);
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
            if (!$this->shouldFail) {
                return array(
                    'id' => 1
                );
            } else {
                return array();
            }
        }
        if ($service == 'Licence' && $method == 'PUT') {
            return array();
        }
    }
}
