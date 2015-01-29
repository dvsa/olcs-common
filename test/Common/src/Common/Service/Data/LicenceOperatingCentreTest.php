<?php

namespace OlcsTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\LicenceOperatingCentre;
use Common\Service\Data\Licence as LicenceService;
use Mockery as m;

/**
 * Class LicenceTest
 * @package OlcsTest\Service\Data
 */
class LicenceOperatingCentreTest extends MockeryTestCase
{
    /**
     * @group licenceOperatingCentreTest
     */
    public function testGetBundle()
    {
        $sut = new LicenceOperatingCentre();

        $bundle = $sut->getBundle();
        $this->assertArrayHasKey('operatingCentres', $bundle['children']);
        $this->assertArrayHasKey(
            'operatingCentre',
            $bundle['children']['operatingCentres']['children']
        );

        $this->assertInternalType('array', $bundle);
    }


    /**
     * @group licenceOperatingCentreTest
     */
    public function testGetId()
    {
        $sut = new LicenceOperatingCentre();
        $licenceId = 110;
        $licenceService = m::mock('Common\Service\Data\Licence');
        $licenceService
            ->shouldReceive('getId')
            ->once()
            ->with()
            ->andReturn($licenceId);
        $sut->setLicenceService($licenceService);

        $this->assertEquals($licenceId, $sut->getId());
    }

    /**
     * @group licenceOperatingCentreTest
     * @dataProvider providerOutputType
     */
    public function testFetchListOptions($outputType)
    {
        $sut = new LicenceOperatingCentre();
        $sut->setOutputType($outputType);
        $licenceId = 110;
        $licenceData = [
            'operatingCentres' => [
                'operatingCentre' => [
                    'operatingCentre' => [
                        'id' => 1,
                        'address' => [
                            'addressLine1' => 'a1',
                            'addressLine2' => 'a2',
                            'addressLine3' => 'a3',
                            'addressLine4' => 'a4',
                            'town' => 'town',
                            'postcode' => 'pc',
                        ]
                    ]
                ]
            ]
        ];

        $licenceService = m::mock('Common\Service\Data\Licence');
        $licenceService
            ->shouldReceive('getId')
            ->times(3)
            ->with()
            ->andReturn($licenceId);

        $licenceService
            ->shouldReceive('fetchOperatingCentreData')
            ->once()
            ->with($licenceId, m::type('array'))
            ->andReturn($licenceData);

        $sut->setLicenceService($licenceService);

        $result = $sut->fetchListOptions($licenceId);

        $this->assertCount(1, $result);
        $this->assertContains('a1', $result[1]);
        $this->assertContains('a2', $result[1]);
        if ($outputType == LicenceOperatingCentre::OUTPUT_TYPE_FULL) {
            $this->assertContains('a3', $result[1]);
            $this->assertContains('pc', $result[1]);
        } else {
            $this->assertContains('town', $result[1]);
        }

        //test data is cached
        $result = $sut->fetchListOptions($licenceId);

        $this->assertCount(1, $result);
        $this->assertContains('a1', $result[1]);
        $this->assertContains('a2', $result[1]);
        if ($outputType == LicenceOperatingCentre::OUTPUT_TYPE_FULL) {
            $this->assertContains('a3', $result[1]);
            $this->assertContains('pc', $result[1]);
        } else {
            $this->assertContains('town', $result[1]);
        }
    }

    public function providerOutputType()
    {
        return [
            [LicenceOperatingCentre::OUTPUT_TYPE_FULL],
            [LicenceOperatingCentre::OUTPUT_TYPE_PARTIAL]
        ];
    }

    /**
     * @group licenceOperatingCentreTest
     */
    public function testSetOutputType()
    {
        $sut = new LicenceOperatingCentre();
        $sut->setOutputType(LicenceOperatingCentre::OUTPUT_TYPE_FULL);
        $this->assertEquals(LicenceOperatingCentre::OUTPUT_TYPE_FULL, $sut->getOutputType());
    }
}
