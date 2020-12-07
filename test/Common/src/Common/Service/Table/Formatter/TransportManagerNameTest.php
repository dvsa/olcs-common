<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\TransportManagerName;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\TransportManagerName
 */
class TransportManagerNameTest extends MockeryTestCase
{
    /** @var TransportManagerName */
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockUrlHelper;

    public function setUp(): void
    {
        $this->sut = new TransportManagerName();

        $this->mockUrlHelper = m::mock();

        $this->sm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $this->sm->shouldReceive('get')->with('Helper\Url')->andReturn($this->mockUrlHelper);
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
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
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
        $expected = '<a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager/details', ['transportManager' => 432], [], true)
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
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
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
        $expected = '<a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('lva-application/transport_manager_details', ['action' => null, 'child_id' => 333], [], true)
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
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
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
        $expected = 'translated <a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager/details', ['transportManager' => 432], [], true)
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
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
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
        $expected = ' <a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager/details', ['transportManager' => 432], [], true)
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
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
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
        $expected = 'translated <a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('lva-variation/transport_manager_details', ['action' => null, 'child_id' => 333], [], true)
            ->andReturn('a-url');

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.updated')
            ->andReturn('translated');
        $this->sm->shouldReceive('get')->once()->with('Helper\Translation')->andReturn($mockTranslator);

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    public function testFormatVariationExternalNoLink()
    {
        $data = [
            'id' => 333,
            'name' => [
                'forename' => 'Arthur',
                'familyName' => 'Smith',
                ],
            'status' => [
                'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
                'description' => 'status description',
            ],
            'transportManager' => [
                'id' => 432
            ],
            'action' => 'D',
        ];
        $column = [
            'lva' => 'variation',
            'internal' => false,
        ];
        $expected = 'translated Arthur Smith';

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.removed')
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
            ],
            'transportManager' => [
                'id' => 432
            ],
        ];
        $column = [
            'lva' => 'licence',
            'internal' => true,
        ];
        $expected = '<a href="a-url">Arthur Smith</a>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager/details', ['transportManager' => 432], [], true)
            ->andReturn('a-url');

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
