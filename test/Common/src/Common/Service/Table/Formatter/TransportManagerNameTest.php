<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Class TransportManagerNameTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class TransportManagerNameTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    private $sut;
    /* @var \Mockery\MockInterface */
    private $sm;
    /* @var \Mockery\MockInterface */
    private $mockUrlHelper;

    public function setUp()
    {
        $this->sut = new \Common\Service\Table\Formatter\TransportManagerName();

        $this->mockUrlHelper = m::mock();

        $this->sm = m::mock('StdClass');
        $this->sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($this->mockUrlHelper);
    }

    public function testFormatNoLvaLocation()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
            ]
        ];
        $column = [];
        $expected = 'Arthur Smith';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatApplicationInternal()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ]
        ];
        $column = [
            'lva' => 'application',
            'internal' => true,
        ];
        $expected = '<b><a href="a-url">Arthur Smith</a></b> <span class="status green">status description</span>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager', ['transportManager' => 432], [], true)
            ->andReturn('a-url');

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatApplicationExternal()
    {
        $data = [
            'id' => 333,
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ]
        ];
        $column = [
            'lva' => 'application',
            'internal' => false,
        ];
        $expected = '<b><a href="a-url">Arthur Smith</a></b> <span class="status green">status description</span>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with(null, ['action' => 'postal-application', 'child_id' => 333], [], true)
            ->andReturn('a-url');

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationInternal()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ],
            'action' => 'U',
        ];
        $column = [
            'lva' => 'variation',
            'internal' => true,
        ];
        $expected = 'translated <b><a href="a-url">Arthur Smith</a></b> '
            . '<span class="status green">status description</span>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager', ['transportManager' => 432], [], true)
            ->andReturn('a-url');

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.updated')
            ->andReturn('translated');
        $this->sm->shouldReceive('get')->once()->with('Helper\Translation')->andReturn($mockTranslator);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationInternalInvalidAction()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ],
        ];
        $column = [
            'lva' => 'variation',
            'internal' => true,
        ];
        $expected = ' <b><a href="a-url">Arthur Smith</a></b> '
            . '<span class="status green">status description</span>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager', ['transportManager' => 432], [], true)
            ->andReturn('a-url');

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationExternal()
    {
        $data = [
            'id' => 333,
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ],
            'action' => 'U',
        ];
        $column = [
            'lva' => 'variation',
            'internal' => false,
        ];
        $expected = 'translated <b><a href="a-url">Arthur Smith</a></b> '
            . '<span class="status green">status description</span>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with(null, ['action' => 'postal-application', 'child_id' => 333], [], true)
            ->andReturn('a-url');

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.updated')
            ->andReturn('translated');
        $this->sm->shouldReceive('get')->once()->with('Helper\Translation')->andReturn($mockTranslator);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatLicenceInternal()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
            ]
        ];
        $column = [
            'lva' => 'licence',
            'internal' => true,
        ];
        $expected = 'Arthur Smith';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatLicenceExternal()
    {
        $data = [
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
            ]
        ];
        $column = [
            'lva' => 'licence',
            'internal' => false,
        ];
        $expected = 'Arthur Smith';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }
}
