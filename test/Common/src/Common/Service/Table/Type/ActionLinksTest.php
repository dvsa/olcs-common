<?php

/**
 * ActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\ActionLinks;
use CommonTest\Bootstrap;

/**
 * ActionLink Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ActionLinksTest extends MockeryTestCase
{
    protected $sut;
    protected $table;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->table = m::mock();
        $this->table->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);

        $this->sut = new ActionLinks($this->table);
    }

    public function testRender()
    {
        $mockTranslate = m::mock()
            ->shouldReceive('translate')
            ->with('action_links.remove')
            ->andReturn('Remove')
            ->once()
            ->shouldReceive('translate')
            ->with('action_links.replace')
            ->andReturn('Replace')
            ->once()
            ->getMock();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'deleteInputName' => 'table[action][delete][%d]',
            'replaceInputName' => 'table[action][replace][%d]',
            'isRemoveVisible' => function ($data) {
                return true;
            },
            'isReplaceVisible' => function ($data) {
                return true;
            },
        ];
        $data = [
            'id' => 123
        ];

        $expected = '<input type="submit" class="right-aligned action--secondary trigger-modal" '.
            'name="table[action][delete][123]" ' .
            'value="Remove"> <input type="submit" class="action--secondary right-aligned trigger-modal" ' .
            'name="table[action][replace][123]" value="Replace">';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    public function testRenderDefault()
    {
        $mockTranslate = m::mock()
            ->shouldReceive('translate')
            ->with('action_links.remove')
            ->andReturn('Remove')
            ->once()
            ->shouldReceive('translate')
            ->with('action_links.replace')
            ->andReturn('Replace')
            ->once()
            ->getMock();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'replaceInputName' => 'table[action][replace][%d]',
        ];
        $data = [
            'id' => 123
        ];

        $expected = '<input type="submit" class="right-aligned action--secondary trigger-modal" '.
            'name="table[action][delete][123]" value="Remove">';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }

    public function testRenderNoModal()
    {
        $mockTranslate = m::mock()
            ->shouldReceive('translate')
            ->with('action_links.remove')
            ->andReturn('Remove')
            ->once()
            ->shouldReceive('translate')
            ->with('action_links.replace')
            ->andReturn('Replace')
            ->once()
            ->getMock();

        $this->sm->setService('translator', $mockTranslate);

        $column = [
            'replaceInputName' => 'table[action][replace][%d]',
            'dontUseModal' => true,
        ];
        $data = [
            'id' => 123
        ];

        $expected = '<input type="submit" class="right-aligned action--secondary" '.
            'name="table[action][delete][123]" value="Remove">';

        $this->assertEquals($expected, $this->sut->render($data, $column));
    }
}
