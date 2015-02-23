<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegGrantNewText3;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegGrantNewText3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantNewText3Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg grant new text 3 filter with an end date
     */
    public function testFilterWithEndDate()
    {
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $via = 'via point';
        $busServices = 'bus services';
        $busServiceTypes = 'bus service types';
        $otherDetails = 'Other details';
        $effectiveDate = '2014-05-14';
        $endDate = '2014-06-30';
        $formattedEffectiveDate = '14 May 2014';
        $formattedEndDate = '30 June 2014';

        $text = "From: " . $startPoint . "\n"
        . "To: " . $finishPoint . "\n"
        . "Via: " . $via . "\n"
        . "Name or No.: " . $busServices . "\n"
        . "Service type: " . $busServiceTypes . "\n"
        . "Effective date: " . $formattedEffectiveDate . "\n"
        . "End date: " . $formattedEndDate . "\n"
        . "Other details: " . $otherDetails;

        $expectedText = sprintf(
            $text,
            $startPoint,
            $finishPoint,
            $via,
            $busServices,
            $busServiceTypes,
            $formattedEffectiveDate,
            $formattedEndDate,
            $otherDetails
        );

        $busRegData = [
            'startPoint' => $startPoint,
            'finishPoint' => $finishPoint,
            'via' => $via,
            'effectiveDate' => $effectiveDate,
            'endDate' => $endDate,
            'otherDetails' => $otherDetails
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'busServiceTypes' => $busServiceTypes
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'busServiceTypes' => $busServiceTypes,
            'text3' => $expectedText
        ];

        $input = new Publication($inputData);
        $sut = new BusRegGrantNewText3();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * @group publicationFilter
     *
     * Test the bus reg grant new text 3 filter without an end date
     */
    public function testFilterWithoutEndDate()
    {
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $via = 'via point';
        $busServices = 'bus services';
        $busServiceTypes = 'bus service types';
        $otherDetails = 'Other details';
        $effectiveDate = '2014-05-14';
        $endDate = null;
        $formattedEffectiveDate = '14 May 2014';

        $text = "From: " . $startPoint . "\n"
            . "To: " . $finishPoint . "\n"
            . "Via: " . $via . "\n"
            . "Name or No.: " . $busServices . "\n"
            . "Service type: " . $busServiceTypes . "\n"
            . "Effective date: " . $formattedEffectiveDate . "\n"
            . "Other details: " . $otherDetails;

        $expectedText = sprintf(
            $text,
            $startPoint,
            $finishPoint,
            $via,
            $busServices,
            $busServiceTypes,
            $formattedEffectiveDate,
            $otherDetails
        );

        $busRegData = [
            'startPoint' => $startPoint,
            'finishPoint' => $finishPoint,
            'via' => $via,
            'effectiveDate' => $effectiveDate,
            'endDate' => $endDate,
            'otherDetails' => $otherDetails
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'busServiceTypes' => $busServiceTypes
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'busServiceTypes' => $busServiceTypes,
            'text3' => $expectedText
        ];

        $input = new Publication($inputData);
        $sut = new BusRegGrantNewText3();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
