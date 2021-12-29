<?php

namespace CommonTest\Data\Mapper\Lva;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Data\Mapper\Lva\UploadEvidence;
use Common\Form\Form;
use Mockery as m;

class UploadEvidenceTest extends MockeryTestCase
{
    public function testMapFromResult()
    {
        $input = [
            'operatingCentres' => [
                [
                    'id' => 1,
                    'adPlacedIn' => 'foo',
                    'adPlacedDate' => '2017-12-01',
                ]
            ]
        ];
        $expected = [
            'operatingCentres' => [
                [
                    'adPlacedIn' => 'foo',
                    'aocId' => 1,
                    'adPlacedDate' => [
                        'day' => 1,
                        'month' => 12,
                        'year' => 2017
                    ]
                ]
            ]
        ];

        $output = UploadEvidence::mapFromResult($input);

        $this->assertEquals($expected, $output);
    }

    public function testMapFromFormNoOperatingCentres(): void
    {
        $this->assertEquals([], UploadEvidence::mapFromForm([]));
    }

    public function testMapFromForm()
    {
        $input = [
            'operatingCentres' => [
                [
                    'aocId' => 1,
                    'adPlacedIn' => 'foo',
                    'adPlacedDate' => '2017-12-01',
                ]
            ]
        ];
        $expected = [
            'operatingCentres' => [
                1 => [
                    'adPlacedIn' => 'foo',
                    'aocId' => 1,
                    'adPlacedDate' => '2017-12-01'
                ]
            ]
        ];

        $output = UploadEvidence::mapFromForm($input);

        $this->assertEquals($expected, $output);
    }

    public function testMapFromResultForm()
    {
        $data = [
            'operatingCentres' => [
                [
                        'id' => 1,
                        'adPlacedIn' => 'foo',
                        'adPlacedDate' => '2017-12-01',
                        'operatingCentre' => [
                            'address' => [
                            'town' => 'bar',
                            'postcode' => 'cake'
                        ]
                    ]
                ]
            ]
        ];
        $mappedData = [
            'operatingCentres' => [
                [
                        'adPlacedIn' => 'foo',
                        'aocId' => 1,
                        'adPlacedDate' => [
                            'day' => 1,
                            'month' => 12,
                            'year' => 2017
                        ]
                ]
            ]
        ];

        $label = 'bar, cake';

        $fieldset = m::mock()
            ->shouldReceive('setLabel')
            ->with($label)
            ->once()
            ->getMock();

        $fieldsets = [$fieldset];

        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('operatingCentres')
            ->andReturn(
                m::mock()
                ->shouldReceive('getFieldsets')
                ->andReturn($fieldsets)
                ->once()
                ->getMock()
            )
            ->once()
            ->shouldReceive('setData')
            ->with($mappedData)
            ->once()
            ->getMock();

        UploadEvidence::mapFromResultForm($data, $mockForm);
    }
}
