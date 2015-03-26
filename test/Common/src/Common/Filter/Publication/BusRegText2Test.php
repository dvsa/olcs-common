<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegText2;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegText2Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegText2Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the bus reg service filter when the organisation has a trading name
     */
    public function testFilterWithTradingName()
    {
        $licNo = '12345';
        $organisationName = 'organisation name';
        $tradingName1 = 'trading name 1';
        $tradingName2 = 'trading name 2';

        $licenceData = [
            'licNo' => $licNo,
            'organisation' => [
                'name' => $organisationName,
                'tradingNames' => [
                    0 => [
                        'name' => $tradingName1
                    ],
                    1 => [
                        'name' => $tradingName2 //always select the latest trading name
                    ]
                ]
            ]
        ];

        $inputData = [
            'licenceData' => $licenceData,
        ];

        $expectedOutput = [
            'licenceData' => $licenceData,
            'text2' => strtoupper($organisationName . ' T/A ' . $tradingName2)
        ];

        $input = new Publication($inputData);
        $sut = new BusRegText2();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    /**
     * @group publicationFilter
     *
     * Test the bus reg service filter when the organisation doesn't have a trading name
     */
    public function testFilterWithNoTradingName()
    {
        $licNo = '12345';
        $organisationName = 'organisation name';

        $licenceData = [
            'licNo' => $licNo,
            'organisation' => [
                'name' => $organisationName,
                'tradingNames' => []
            ]
        ];

        $inputData = [
            'licenceData' => $licenceData,
        ];

        $expectedOutput = [
            'licenceData' => $licenceData,
            'text2' => strtoupper($organisationName)
        ];

        $input = new Publication($inputData);
        $sut = new BusRegText2();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }
}
