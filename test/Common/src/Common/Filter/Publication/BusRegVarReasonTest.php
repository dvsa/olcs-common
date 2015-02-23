<?php

namespace CommonTest\Filter\Publication;

use Common\Filter\Publication\BusRegVarReason;
use Common\Data\Object\Publication;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Class BusRegVarReasonTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegVarReasonTest extends MockeryTestCase
{
    /**
     * @param $variationReasons
     * @param $outputString
     *
     * @dataProvider filterProvider
     *
     * @group publicationFilter
     *
     * Test the bus reg variation reason filter
     */
    public function testFilter($variationReasons, $outputString)
    {
        $busRegData = [
            'variationReasons' => $variationReasons
        ];

        $inputData = [
            'busRegData' => $busRegData,
        ];

        $expectedOutput = [
            'busRegData' => $busRegData,
            'variationReasons' => $outputString
        ];

        $input = new Publication($inputData);
        $sut = new BusRegVarReason();

        $output = $sut->filter($input);

        $this->assertEquals($expectedOutput, $output->getArrayCopy());
    }

    public function filterProvider()
    {
        $reason1 = 'reason 1';
        $reason2 = 'reason 2';
        $reason3 = 'reason 3';

        return [
            [
                [], null
            ],
            [
                [
                    0 => [
                    'description' => $reason1
                    ]
                ],
                $reason1
            ],
            [
                [
                    0 => [
                        'description' => $reason1
                    ],
                    1 => [
                        'description' => $reason2
                    ]
                ],
                $reason1 . ' and ' . $reason2
            ],
            [
                [
                    0 => [
                        'description' => $reason1
                    ],
                    1 => [
                        'description' => $reason2
                    ],
                    2 => [
                        'description' => $reason3
                    ]
                ],
                $reason1 . ', ' . $reason2 . ' and ' . $reason3
            ]
        ];
    }
}
