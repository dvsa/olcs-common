<?php

/**
 * Get Placeholder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\View\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\GetPlaceholder;
use Zend\View\Model\ViewModel;

/**
 * Get Placeholder Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholderTest extends MockeryTestCase
{
    protected $container;

    protected $sut;

    public function setUp()
    {
        $this->container = m::mock();

        $this->sut = new GetPlaceholder($this->container);
    }

    /**
     * @dataProvider asStringProvider
     */
    public function testAsString($value, $expected)
    {
        $this->container->shouldReceive('getValue')->andReturn($value);

        $this->assertEquals($expected, $this->sut->asString());
    }

    /**
     * @dataProvider asViewProvider
     */
    public function testAsView($value, $expected)
    {
        $this->container->shouldReceive('getValue')->andReturn($value);

        $this->assertEquals($expected, $this->sut->asView());
    }

    /**
     * @dataProvider asObjectProvider
     */
    public function testAsObject($value, $expected)
    {
        $this->container->shouldReceive('getValue')->andReturn($value);

        $this->assertEquals($expected, $this->sut->asObject());
    }

    /**
     * @dataProvider asBoolProvider
     */
    public function testAsBool($value, $expected)
    {
        $this->container->shouldReceive('getValue')->andReturn($value);

        $this->assertEquals($expected, $this->sut->asBool());
    }

    public function asStringProvider()
    {
        return [
            [
                ['foo'],
                'foo'
            ],
            [
                'foo',
                'foo'
            ],
            [
                [
                    ['foo']
                ],
                null
            ]
        ];
    }

    public function asViewProvider()
    {
        $view = new ViewModel();

        return [
            [
                [$view],
                $view
            ],
            [
                $view,
                $view
            ],
            [
                [
                    [$view]
                ],
                null
            ],
            [
                'foo',
                null
            ]
        ];
    }

    public function asObjectProvider()
    {
        $class = new \stdClass();

        return [
            [
                [$class],
                $class
            ],
            [
                $class,
                $class
            ],
            [
                [
                    [$class]
                ],
                null
            ],
            [
                'foo',
                null
            ]
        ];
    }

    public function asBoolProvider()
    {
        return [
            [
                [true],
                true
            ],
            [
                true,
                true
            ],
            [
                [
                    [true]
                ],
                null
            ],
            [
                'foo',
                null
            ]
        ];
    }
}
