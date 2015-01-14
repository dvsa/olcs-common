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

    public function testFetchListOptions()
    {
        $sut = new LicenceOperatingCentre();
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
        $this->assertContains('a3', $result[1]);
        $this->assertContains('pc', $result[1]);

        //test data is cached
        $result = $sut->fetchListOptions($licenceId);

        $this->assertCount(1, $result);
        $this->assertContains('a1', $result[1]);
        $this->assertContains('a2', $result[1]);
        $this->assertContains('a3', $result[1]);
        $this->assertContains('pc', $result[1]);

    }
}
