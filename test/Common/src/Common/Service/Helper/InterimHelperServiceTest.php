<?php

namespace CommonTest\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

use CommonTest\Bootstrap;
use Common\Service\Helper\InterimHelperService;

/**
 * Class InterimHelperServiceTest
 *
 * Test the interim helper service view determining logical methods.
 *
 * @package CommonTest\Service\Helper
 */
class InterimHelperServiceTest extends MockeryTestCase
{
    protected $sut = null;
    protected $sm = null;

    public function setUp()
    {
        $this->sut = new InterimHelperService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function canVariationInterimTrueProvider()
    {
        return array(
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sn'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_sn'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_si'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_sn'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'totAuthVehicles' => 10,
                    'licence' => array(
                        'totAuthVehicles' => 11
                    )
                ),
                array('hasVehicleAuthChange' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthVehicles' => 'null',
                    'licence' => array(
                        'totAuthVehicles' => 10
                    )
                ),
                array('hasVehicleAuthChange' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 10,
                    'licence' => array(
                        'totAuthTrailers' => 11
                    )
                ),
                array('hasTrailerAuthChange' => 'totAuthTrailers')
            ),
            array(
                array(
                    'totAuthTrailers' => 'null',
                    'licence' => array(
                        'totAuthTrailers' => 11
                    )
                ),
                array('hasTrailerAuthChange' => 'totAuthTrailers')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        'a'=> 'b'
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            'a'=> 'b'
                        )
                    )
                ),
                array('hasNewOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'noOfVehiclesRequired' => 11,
                            'noOfTrailersRequired' => 10
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'noOfVehiclesRequired' => 10,
                            'noOfTrailersRequired' => 11
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            )
        );
    }

    public function canVariationInterimFalseProvider()
    {
        return array(
            array(
                array(
                    'licenceType' => array(
                        'id' => 'ltyp_sr'
                    ),
                    'licence' => array(
                        'licenceType' => array(
                            'id' => 'ltyp_r'
                        )
                    )
                ),
                array('hasUpgrade'=> 'licenceType')
            ),
            array(
                array(
                    'totAuthVehicles' => 10,
                    'licence' => array(
                        'totAuthVehicles' => 10
                    )
                ),
                array('hasVehicleAuthChange' => 'totAuthVehicles')
            ),
            array(
                array(
                    'totAuthTrailers' => 10,
                    'licence' => array(
                        'totAuthTrailers' => 10
                    )
                ),
                array('hasTrailerAuthChange' => 'totAuthTrailers')
            ),
            array(
                array(
                    'operatingCentres' => array(),
                    'licence' => array(
                        'operatingCentres' => array(
                            'a'=> 'b'
                        )
                    )
                ),
                array('hasNewOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(
                        array(
                            'noOfVehiclesRequired' => 10,
                            'noOfTrailersRequired' => 10
                        )
                    ),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            ),
            array(
                array(
                    'operatingCentres' => array(),
                    'licence' => array(
                        'operatingCentres' => array(
                            array(
                                'noOfVehiclesRequired' => 10,
                                'noOfTrailersRequired' => 10
                            )
                        )
                    )
                ),
                array('hasIncreaseInOperatingCentre' => 'operatingCentres')
            )
        );
    }

    /**
     * @dataProvider canVariationInterimTrueProvider
     */
    public function testCanVariationTrueInterim($interimData, $functionAndKey)
    {
        $applicationId = 123;

        $this->sut->setFunctionToDataMap($functionAndKey);

        $this->sm->shouldReceive('get')
            ->with('Entity/Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVariationInterimData')
                    ->andReturn($interimData)
                    ->getMock()
            );

        $this->assertTrue($this->sut->canVariationInterim($applicationId));
    }

    /**
     * @dataProvider canVariationInterimFalseProvider
     */
    public function testCanVariationFalseInterim($interimData, $functionAndKey)
    {
        $applicationId = 123;

        $this->sut->setFunctionToDataMap($functionAndKey);

        $this->sm->shouldReceive('get')
            ->with('Entity/Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVariationInterimData')
                    ->andReturn($interimData)
                    ->getMock()
            );

        $this->assertFalse($this->sut->canVariationInterim($applicationId));
    }

    public function testCreateInterimFeeIfNotExist()
    {
        $applicationId = 123;

        $this->sm->shouldReceive('get')
            ->with('Processing\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeTypeForApplication')
                    ->with(123, 'GRANTINT')
                    ->shouldReceive('createFee')
                    ->with(123, null, 'GRANTINT')
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Entity\Fee')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeByTypeStatusesAndApplicationId')
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Entity\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDataForInterim')
                    ->getMock()
            );

        $this->sut->createInterimFeeIfNotExist($applicationId);
    }

    public function testCancelInterimFees()
    {
        $applicationId = 123;

        $this->sm->shouldReceive('get')
            ->with('Entity\Fee')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeByTypeStatusesAndApplicationId')
                    ->andReturn(
                        array(
                            array('id' => 1)
                        )
                    )
                    ->shouldReceive('cancelByIds')
                    ->with(array(1))
                    ->getMock()
            );

        $this->sm->shouldReceive('get')
            ->with('Processing\Application')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getFeeTypeForApplication')
                    ->with(123, 'GRANTINT')
                    ->getMock()
            );

        $this->sut->cancelInterimFees($applicationId);
    }

    public function testCanVariationInterimInvalidProvider()
    {
        return array(
            array(null),
            array("string")
        );
    }

    /**
     * @dataProvider testCanVariationInterimInvalidProvider
     */
    public function testCanVariationInterimThrowsExpection($applicationId)
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->sut->canVariationInterim($applicationId);
    }
}
