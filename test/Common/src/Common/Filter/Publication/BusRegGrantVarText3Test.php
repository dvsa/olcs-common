<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegGrantVarText3;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegGrantVarText3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegGrantVarText3Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg grant cancel text 3 filter
     */
    public function testFilterWithVariationReasons()
    {
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $busServices = 'bus services';
        $effectiveDate = '2014-05-14';
        $formattedEffectiveDate = '14 May 2014';
        $variationReasons = 'variation reasons';
        $text = 'Operating between %s and %s given service number %s effective from %s. To amend %s.';

        $expectedText = sprintf(
            $text,
            $startPoint,
            $finishPoint,
            $busServices,
            $formattedEffectiveDate,
            $variationReasons
        );

        $busRegData = [
            'startPoint' => $startPoint,
            'finishPoint' => $finishPoint,
            'effectiveDate' => $effectiveDate,
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'variationReasons' => $variationReasons
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'variationReasons' => $variationReasons,
            'text3' => $expectedText
        ];

        $input = new Publication($inputData);
        $sut = new BusRegGrantVarText3();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * @group publicationFilter
     *
     * Test the bus reg grant var text 3 filter with no variation reasons
     */
    public function testFilterWithNoVariationReasons()
    {
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $busServices = 'bus services';
        $effectiveDate = '2014-05-14';
        $formattedEffectiveDate = '14 May 2014';
        $variationReasons = null;
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
            'effectiveDate' => $effectiveDate,
        ];

        $inputData = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'variationReasons' => $variationReasons
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'busServices' => $busServices,
            'variationReasons' => $variationReasons,
            'text3' => $expectedText
        ];

        $input = new Publication($inputData);
        $sut = new BusRegGrantVarText3();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
