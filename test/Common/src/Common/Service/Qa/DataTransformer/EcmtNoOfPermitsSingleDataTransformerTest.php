<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\DataTransformer\EcmtNoOfPermitsSingleDataTransformer;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * EcmtNoOfPermitsSingleDataTransformerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EcmtNoOfPermitsSingleDataTransformerTest extends MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = new EcmtNoOfPermitsSingleDataTransformer();
    }

    /**
     * @dataProvider dpGetTransformed
     */
    public function testGetTransformed($data, $expectedTransformedData)
    {
        $this->assertEquals(
            $expectedTransformedData,
            $this->sut->getTransformed($data)
        );
    }

    public function dpGetTransformed()
    {
        return [
            [
                [
                    'emissionsCategory' => 'euro5',
                    'permitsRequired' => '12'
                ],
                [
                    'euro5' => '12',
                    'euro6' => '0'
                ]
            ],
            [
                [
                    'emissionsCategory' => 'euro6',
                    'permitsRequired' => '8'
                ],
                [
                    'euro5' => '0',
                    'euro6' => '8'
                ]
            ]
        ];
    }

    public function testGetTransformedNoPermitsRequiredKey()
    {
        $data = [
            'euro5' => '12',
            'euro6' => '6'
        ];

        $this->assertEquals(
            $data,
            $this->sut->getTransformed($data)
        );
    }

    /**
     * @dataProvider dpGetTransformedUnexpectedData
     */
    public function testGetTransformedUnexpectedData($data)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(EcmtNoOfPermitsSingleDataTransformer::ERR_UNEXPECTED_DATA);

        $this->sut->getTransformed($data);
    }

    public function dpGetTransformedUnexpectedData()
    {
        return [
            [
                [
                    'permitsRequired' => '7',
                    'euro5' => '6'
                ]
            ],
            [
                [
                    'permitsRequired' => '8',
                    'euro5' => '12'
                ]
            ],
            [
                [
                    'permitsRequired' => '10',
                    'euro5' => '5',
                    'euro6' => '7'
                ]
            ],
        ];
    }
}
