<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\VehicleAuthorisation;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class VehicleAuthorisationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class VehicleAuthorisationTest extends MockeryTestCase
{
    /**
     * @dataProvider filterProvider
     * @group publicationFilter
     *
     * @param string $licType
     * @param int $totVehicles
     * @param int $totTrailers
     * @param array $newOutput
     *
     * Tests the filter
     */
    public function testFilter($licType, $totVehicles, $totTrailers, $newOutput)
    {
        $appData = [
            'totAuthVehicles' => $totVehicles,
            'totAuthTrailers' => $totTrailers
        ];

        $inputData = [
            'applicationData' => $appData,
            'licType' => $licType
        ];

        $expectedOutput = array_merge($inputData, $newOutput);

        $input = new Publication($inputData);
        $sut = new VehicleAuthorisation();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function filterProvider()
    {
        $sut = new VehicleAuthorisation();

        return [
            [$sut::GV_LIC_TYPE, 4, 2, ['authorisation' => 'Authorisation: 4 Vehicle(s) and 2 Trailer(s)']],
            [$sut::GV_LIC_TYPE, 0, 2, ['authorisation' => 'Authorisation: 2 Trailer(s)']],
            ['other_type', 3, 0, ['authorisation' => 'Authorisation: 3 Vehicle(s)']],
            ['other_type', 0, 0, []],
        ];
    }
}
