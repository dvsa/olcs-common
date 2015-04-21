<?php

/**
 * Sum Context Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Form\Elements\Validators;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Form\Elements\Validators\SumContext;

/**
 * Sum Context Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SumContextTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new SumContext();
    }

    /**
     * @dataProvider providerIsValid
     */
    public function testIsValid($min, $max, $context, $expected)
    {
        $this->sut->setMin($min);
        $this->sut->setMax($max);

        $this->assertEquals($expected, $this->sut->isValid(null, $context));
    }

    public function providerIsValid()
    {
        return [
            [
                null,
                10,
                [
                    'foo' => 3,
                    'bar' => 3
                ],
                true
            ],
            [
                null,
                10,
                [
                    'foo' => 3,
                    'bar' => 3,
                    'cake' => 3,
                    'box' => 3
                ],
                false
            ],
            [
                null,
                10,
                [
                    'foo' => 3,
                    'bar' => 3,
                    'cake' => 3,
                    'box' => 1
                ],
                true
            ],
            [
                10,
                null,
                [
                    'foo' => 3,
                    'bar' => 3,
                    'cake' => 3,
                    'box' => 1
                ],
                true
            ],
            [
                10,
                null,
                [
                    'foo' => 3,
                    'bar' => 3,
                    'cake' => 3
                ],
                false
            ],
            [
                10,
                null,
                [
                    'foo' => 3,
                    'bar' => 3,
                    'cake' => 3,
                    'box' => 3
                ],
                true
            ],
            [
                1,
                5,
                [
                    'foo' => 1
                ],
                true
            ],
            [
                1,
                5,
                [
                    'foo' => 5
                ],
                true
            ],
            [
                1,
                5,
                [
                    'foo' => 0
                ],
                false
            ],
            [
                1,
                5,
                [
                    'foo' => 6
                ],
                false
            ]
        ];
    }
}
