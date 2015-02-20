<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegGrantCancelText3;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegGrantCancelText3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantCancelText3Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg grant cancel text 3 filter
     */
    public function testFilter()
    {
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $busServices = 'bus services';
        $effectiveDate = '2014-05-14';
        $formattedEffectiveDate = '14 May 2014';
        $text = 'Operating between %s and %s given service number %s effective from %s.';

        $expectedText = sprintf(
            $text,
            $startPoint,
            $finishPoint,
            $busServices,
            $formattedEffectiveDate
        );


        $busRegData = [
            'startPoint' => $startPoint,
            'finishPoint' => $finishPoint,
            'effectiveDate' => $effectiveDate
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'busServices' => $busServices
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'text3' => $expectedText
        ];

        $input = new Publication($inputData);
        $sut = new BusRegGrantCancelText3();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
