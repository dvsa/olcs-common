<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegServiceTypes;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegServiceTypesTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegServiceTypesTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg service types filter
     */
    public function testFilter()
    {
        $firstServiceType = 'type 1';
        $secondServiceType = 'type 2';
        $thirdServiceType = 'type 3';
        $delimiter = ', ';

        $busRegData = [
            'busServiceTypes' => [
                0 => [
                    'description' => $firstServiceType
                ],
                1 => [
                    'description' => $secondServiceType
                ],
                2 => [
                    'description' => $thirdServiceType
                ]
            ]
        ];

        $inputData = [
            'busRegData' => $busRegData,
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServiceTypes' => $firstServiceType . $delimiter . $secondServiceType . $delimiter . $thirdServiceType
        ];

        $input = new Publication($inputData);
        $sut = new BusRegServiceTypes();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
