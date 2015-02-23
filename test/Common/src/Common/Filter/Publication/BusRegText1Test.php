<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegText1;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegText1Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegText1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg text 1 filter
     */
    public function testFilter()
    {
        $regNo = '12345';

        $busRegData = [
            'regNo' => $regNo
        ];

        $inputData = [
            'busRegData' => $busRegData,
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'text1' => $regNo
        ];

        $input = new Publication($inputData);
        $sut = new BusRegText1();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
