<?php

/**
 * Fee common service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Fee;

use Common\Service\Fee\FeeCommon;
use CommonTest\Bootstrap;

/**
 * Fee common service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FeeCommonTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Fee service
     */
    public $fee;

    /**
     * @var string
     */
    protected $niFlag;

    /**
     * @var string
     */
    protected $licenceType;

    /**
     * @var string
     */
    protected $goodsOrPsv;

    /**
     * @var string
     */
    protected $fixedValue;

    /**
     * @var string
     */
    protected $effectiveFrom;

    /**
     * @var string
     */
    protected $trafficArea;

    /**
     * @var string
     */
    protected $feeTypeLicenceType;

    /**
     * @var string
     */
    protected $feeTypeGoodsOrPsv;

    /**
     * Set up the fee service
     */
    public function setUp()
    {
        $this->fee = $this->getMock('Common\Service\Fee\FeeCommon', array('makeRestCall'));
        $this->fee->expects($this->any())
            ->method('makeRestCall')
            ->will($this->returnCallback(array($this, 'mockRestCalls')));
    }

    /**
     * Test create service
     * @group feeService
     */
    public function testCreateService()
    {
        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager');
        $this->assertSame($this->fee, $this->fee->createService($mockServiceLocator));
    }

    /**
     * Test generate fee for not implemented type
     * @expectedException \Common\Service\Fee\Exception
     * @group feeService
     */
    public function testGenerateFeeNotImplemented()
    {
        $this->fee->generateFee('FOO');
    }

    /**
     * Test generate fee with no appliction id
     * @expectedException \Common\Service\Fee\Exception
     * @group feeService
     */
    public function testGenerateFeeNoApplicationId()
    {
        $this->fee->generateFee('APP');
    }

    /**
     * Test generate application fee - no fee found
     * 
     * @expectedException \Common\Service\Fee\Exception
     * @dataProvider providerGenerateFeeNoFeeFoundData
     * @group feeService
     */
    public function testGenerateFeeNoFeeFound(
        $niFlag,
        $licenceType,
        $goodsOrPsv,
        $effectiveFrom,
        $trafficArea,
        $feeTypeLicenceType,
        $feeTypeGoodsOrPsv
    ) {
        $this->niFlag = $niFlag;
        $this->licenceType = $licenceType;
        $this->goodsOrPsv = $goodsOrPsv;
        $this->fixedValue = '100.00';
        $this->effectiveFrom = $effectiveFrom;
        $this->trafficArea = $trafficArea;
        $this->feeTypeLicenceType = $feeTypeLicenceType;
        $this->feeTypeGoodsOrPsv = $feeTypeGoodsOrPsv;
        $this->fee->generateFee('APP', 1);
    }

    /**
     * Data provider for test generate fee
     */
    public function providerGenerateFeeNoFeeFoundData()
    {
        return [
            // no fee type for provided licence type
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', 'ltyp_si', 'lcat_psv'],
            // no fee type for provided operator type
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', 'ltyp_sn', 'lcat_gv'],
            // no fee type - effective from is more than application date
            ['N', 'ltyp_sn', 'lcat_psv', '2999-01-01', 'K', 'ltyp_sn', 'lcat_gv'],
            // no fee type for Northern Ireland application
            ['Y', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', 'ltyp_sn', 'lcat_psv'],
            // no fee type for non Northern Ireland application
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'N', 'ltyp_sn', 'lcat_psv'],
        ];
    }

    /**
     * Test generate application fee
     * 
     * @dataProvider providerGenerateFeeData
     * @group feeService
     */
    public function testGenerateFee(
        $niFlag,
        $licenceType,
        $goodsOrPsv,
        $effectiveFrom,
        $trafficArea,
        $feeTypeLicenceType,
        $feeTypeGoodsOrPsv,
        $fixedValue,
        $expectedAmount
    ) {
        $this->niFlag = $niFlag;
        $this->licenceType = $licenceType;
        $this->goodsOrPsv = $goodsOrPsv;
        $this->fixedValue = $fixedValue;
        $this->effectiveFrom = $effectiveFrom;
        $this->trafficArea = $trafficArea;
        $this->feeTypeLicenceType = $feeTypeLicenceType;
        $this->feeTypeGoodsOrPsv = $feeTypeGoodsOrPsv;
        $params = [
            'amount' => $expectedAmount,
            'application' => 1,
            'licence' => 1,
            'invoicedDate' => date('Y-m-d'),
            'feeType' => 1,
            'description' => 'description for application 1',
            'feeStatus' => 'lfs_ot',
            'createdBy' => 2,
            'lastModifiedBy' => 2,
            'lastModifiedOn' => date('Y-m-d H:s:i')
        ];
        $this->fee->expects($this->at(2))
            ->method('makeRestCall')
            ->with('Fee', 'POST', $params)
            ->will($this->returnCallback(array($this, 'mockRestCalls')));

        $this->fee->generateFee('APP', 1);
    }

    /**
     * Data provider for test generate fee
     */
    public function providerGenerateFeeData()
    {
        return [
            // found fee type
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', 'ltyp_sn', 'lcat_psv', '100.00', '100.00'],
            // found fee type, used five year value
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', 'ltyp_sn', 'lcat_psv', '0.00', '150.00'],
            // found fee type for Northern Ireland
            ['Y', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'N', 'ltyp_sn', 'lcat_psv', '100.00', '100.00'],
            // found fee type with empty licence type
            ['N', 'ltyp_sn', 'lcat_psv', '2014-01-01', 'K', null, 'lcat_psv', '100.00', '100.00'],
        ];
    }

    /**
     * Mock the rest call
     *
     * @param string $service
     * @param string $method
     * @param array $data
     */
    public function mockRestCalls($service, $method, $data = [])
    {
        if ($service == 'Application' && $method == 'GET') {
            return [
                'receivedDate' => '2014-01-01',
                'createdOn' => '2014-01-01',
                'licence' => [
                    'id' => 1,
                    'niFlag' => $this->niFlag,
                    'licenceType' => [
                        'id' => $this->licenceType
                    ],
                    'goodsOrPsv' => [
                        'id' => $this->goodsOrPsv
                    ]
                ]
            ];
        }
        if ($service == 'FeeType' && $method == 'GET') {
            if ($data['goodsOrPsv'] == $this->feeTypeGoodsOrPsv && substr($data['effectiveFrom'], 3) >=
                $this->effectiveFrom) {
                if (is_null($this->feeTypeLicenceType)) {
                    $licenceType = [];
                } else {
                    $licenceType = ['id' => $this->feeTypeLicenceType];
                }
                return [
                    'Results' => [[
                        'id' => 1,
                        'description' => 'description',
                        'fixedValue' => $this->fixedValue,
                        'fiveYearValue' => 150.00,
                        'effectiveFrom' => $this->effectiveFrom,
                        'trafficArea' => [
                            'id' => $this->trafficArea
                        ],
                        'licenceType' => $licenceType,
                        'goodsOrPsv' => [
                            'id' => $this->feeTypeGoodsOrPsv
                        ]
                    ]],
                    'Count' => 1
                ];
            } else {
                return [
                    'Results' => [],
                    'Count' => 0
                ];
            }
        }
    }
}
