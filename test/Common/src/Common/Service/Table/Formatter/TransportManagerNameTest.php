<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\TransportManagerApplicationEntityService;
use Common\Service\Table\Formatter\TransportManagerName;

/**
 * Class TransportManagerNameTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class TransportManagerNameTest extends MockeryTestCase
{
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockUrlHelper;

    public function setUp()
    {
        $this->sut = new TransportManagerName();

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
        $expected = '<b><a href="a-url">Arthur Smith</a></b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager', ['transportManager' => 432], [], true)
            ->andReturn('a-url');

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }

    protected function mockGetStatusHtml($expectedStatusId, $expectedStatusDescription, $statusHtml = '<STATUS HTML>')
    {
        $mockViewHelperManager = m::mock();
        $mockViewHelper = m::mock();

        $this->sm->shouldReceive('get')
            ->with('ViewHelperManager')
            ->once()
            ->andReturn($mockViewHelperManager);

        $mockViewHelperManager->shouldReceive('get')
            ->with('transportManagerApplicationStatus')
            ->once()
            ->andReturn($mockViewHelper);

        $mockViewHelper->shouldReceive('render')
            ->with($expectedStatusId, $expectedStatusDescription)
            ->once()
            ->andReturn($statusHtml);
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
        $expected = '<b><a href="a-url">Arthur Smith</a></b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

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
        $expected = 'translated <b><a href="a-url">Arthur Smith</a></b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

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
        $expected = ' <b><a href="a-url">Arthur Smith</a></b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

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
        $expected = 'translated <b><a href="a-url">Arthur Smith</a></b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

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
                'id' => TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
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
        $expected = 'translated <b>Arthur Smith</b> <STATUS HTML>';

        $this->mockGetStatusHtml($data['status']['id'], $data['status']['description']);

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
        $expected = '<b><a href="a-url">Arthur Smith</a></b>';

        $this->mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('transport-manager', ['transportManager' => 432], [], true)
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
