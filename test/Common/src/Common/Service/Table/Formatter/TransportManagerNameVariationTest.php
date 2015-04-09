<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class TransportManagerNameTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class TransportManagerNameVariationTest extends MockeryTestCase
{
    public function testFormatUpdatedAction()
    {
        $mockUrlHelper = m::mock();
        $mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with(null, ['action' => 'postal-application', 'child_id' => 111], [], true)
            ->andReturn('a-url');

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.updated')
            ->andReturn('UPDATED');

        $mockServerManager = m::mock();
        $mockServerManager->shouldReceive('get')->with('Helper\Url')->andReturn($mockUrlHelper);
        $mockServerManager->shouldReceive('get')->with('Helper\Translation')->andReturn($mockTranslator);

        $sut = new \Common\Service\Table\Formatter\TransportManagerNameVariation();
        $data = [
            'id' => 111,
            'forename' => 'Arthur',
            'familyName' => 'Smith',
            'status' => [
                'id' => \Common\Service\Entity\TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'a description',
            ],
            'action' => 'U',
        ];

        $formatted = $sut->format($data, null, $mockServerManager);

        $this->assertEquals(
            'UPDATED <b><a href="a-url">Arthur Smith</a></b> <span class="status green">a description</span>',
            $formatted
        );
    }

    public function testFormatRemovedAction()
    {
        $mockUrlHelper = m::mock();
        $mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with(null, ['action' => 'postal-application', 'child_id' => 111], [], true)
            ->andReturn('a-url');

        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')
            ->once()
            ->with('tm_application.table.status.removed')
            ->andReturn('REMOVED');

        $mockServerManager = m::mock();
        $mockServerManager->shouldReceive('get')->with('Helper\Url')->andReturn($mockUrlHelper);
        $mockServerManager->shouldReceive('get')->with('Helper\Translation')->andReturn($mockTranslator);

        $sut = new \Common\Service\Table\Formatter\TransportManagerNameVariation();
        $data = [
            'id' => 111,
            'forename' => 'Arthur',
            'familyName' => 'Smith',
            'status' => [
                'id' => \Common\Service\Entity\TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'a description',
            ],
            'action' => 'D',
        ];

        $formatted = $sut->format($data, null, $mockServerManager);

        $this->assertEquals(
            'REMOVED <b>Arthur Smith</b> <span class="status green">a description</span>',
            $formatted
        );
    }

    public function testFormatInvalidActionForTranslator()
    {
        $mockUrlHelper = m::mock();
        $mockUrlHelper->shouldReceive('fromRoute')
            ->once()
            ->with(null, ['action' => 'postal-application', 'child_id' => 111], [], true)
            ->andReturn('a-url');

        $mockServerManager = m::mock();
        $mockServerManager->shouldReceive('get')->with('Helper\Url')->andReturn($mockUrlHelper);

        $sut = new \Common\Service\Table\Formatter\TransportManagerNameVariation();
        $data = [
            'id' => 111,
            'forename' => 'Arthur',
            'familyName' => 'Smith',
            'status' => [
                'id' => \Common\Service\Entity\TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION,
                'description' => 'a description',
            ],
            'action' => 'X',
        ];

        $this->setExpectedException('InvalidArgumentException');
        $sut->format($data, null, $mockServerManager);
    }
}
