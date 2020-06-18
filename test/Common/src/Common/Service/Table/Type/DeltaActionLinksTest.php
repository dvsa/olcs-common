<?php

/**
 * DeltaActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\DeltaActionLinks;
use CommonTest\Bootstrap;

/**
 * DeltaActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DeltaActionLinksTest extends MockeryTestCase
{
    protected $sut;
    protected $table;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->table = m::mock();
        $this->table->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);

        $this->sut = new DeltaActionLinks($this->table);
    }

    /**
     * @dataProvider tableDataProvider
     */
    public function testRender($data, $expected)
    {
        $mockTranslate = m::mock()
            ->shouldReceive('translate')
            ->with('delta_action_links.remove')
            ->andReturn('Remove')
            ->once()
            ->shouldReceive('translate')
            ->with('delta_action_links.restore')
            ->andReturn('Restore')
            ->once()
            ->getMock();

        $this->sm->setService('translator', $mockTranslate);

        $this->assertEquals($expected, $this->sut->render($data, []));
    }

    public function tableDataProvider()
    {
        return [
            [
                [
                    'id' => 123,
                    'action' => 'A'
                ],
                '<input type="submit" class="right-aligned action--secondary trigger-modal" '.
                    'name="table[action][delete][123]" value="Remove">'
            ],
            [
                [
                    'id' => 456,
                    'action' => 'D'
                ],
                '<input type="submit" class="right-aligned action--secondary" '.
                    'name="table[action][restore][456]" value="Restore">'
            ],
            [
                [
                    'id' => 789
                ],
                ''
            ]
        ];
    }
}
