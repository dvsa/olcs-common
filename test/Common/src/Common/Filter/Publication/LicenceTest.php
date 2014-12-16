<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\Licence;
use Common\Data\Object\Publication;
use Mockery as m;

/**
 * Class LicenceTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests exception thrown if there is no licence
     *
     * @group publicationFilter
     *
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testNoLicenceException()
    {
        $input = new Publication();
        $sut = new Licence();

        $mockLicenceService = m::mock('Common\Service\Data\Licence');
        $mockLicenceService->shouldReceive('fetchLicenceData')->andReturn(false);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $sut->setServiceLocator($mockServiceManager);

        $sut->filter($input);
    }

    /**
     * @dataProvider filterProvider
     *
     * @group publicationFilter
     *
     * @param string $goodsOrPsv
     * @param string $expectedPubType
     */
    public function testFilter($goodsOrPsv, $expectedPubType)
    {
        $trafficArea = 'B';
        $licenceId = 7;

        $licenceData = [
            'id' => $licenceId,
            'trafficArea' => [
                'id' => $trafficArea
            ],
            'goodsOrPsv' => [
                'id' => $goodsOrPsv
            ]
        ];

        $expectedOutput = [
            'pubType' => $expectedPubType,
            'licence' => $licenceId,
            'trafficArea' => $trafficArea,
            'licenceData' => $licenceData
        ];

        $input = new Publication();
        $sut = new Licence();

        $mockLicenceService = m::mock('Common\Service\Data\Licence');
        $mockLicenceService->shouldReceive('fetchLicenceData')->andReturn($licenceData);

        $mockServiceManager = m::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get')->with('\Common\Service\Data\Licence')->andReturn($mockLicenceService);

        $sut->setServiceLocator($mockServiceManager);

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * Provider for testFilter
     *
     * @return array
     */
    public function filterProvider()
    {
        return [
            ['lcat_gv','A&D'],
            ['lcat_psv','N&P']
        ];
    }
}
