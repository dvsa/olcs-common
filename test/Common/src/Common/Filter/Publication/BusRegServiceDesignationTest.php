<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegServiceDesignation;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegServiceDesignationTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegServiceDesignationTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg service designation filter
     */
    public function testFilter()
    {
        $serviceNo = 12345;
        $firstOtherService = 23456;
        $secondOtherService = 34567;
        $delimiter = ' / ';

        $busRegData = [
            'serviceNo' => $serviceNo,
            'otherServices' => [
                0 => [
                    'serviceNo' => $firstOtherService
                ],
                1 => [
                    'serviceNo' => $secondOtherService
                ]
            ]
        ];

        $inputData = [
            'busRegData' => $busRegData,
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $serviceNo . $delimiter . $firstOtherService . $delimiter . $secondOtherService
        ];

        $input = new Publication($inputData);
        $sut = new BusRegServiceDesignation();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
