<?php

/**
 * VariationOperatingCentreAction Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\VariationOperatingCentreAction;

/**
 * VariationOperatingCentreAction Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreActionTest extends MockeryTestCase
{
    protected $sut;
    protected $table;

    public function setUp()
    {
        $this->table = m::mock();

        $this->sut = new VariationOperatingCentreAction($this->table);
    }

    /**
     * @dataProvider provider
     */
    public function testRender($action, $expected)
    {
        $this->table->shouldReceive('getFieldset')
            ->andReturn('table');

        $data = [
            'id' => 7,
            'link' => 'link-text',
            'action' => $action
        ];
        $column = [
            'action' => 'foo',
            'name' => 'link'
        ];

        $response = $this->sut->render($data, $column);

        $this->assertEquals(
            $expected,
            $response
        );
    }

    public function provider()
    {
        return [
            [
                'action' => 'A',
                '(New) <input type="submit" class="" name="table[action][foo][7]" value="link-text"  />'
            ],
            [
                'action' => 'U',
                '(Updated) <input type="submit" class="" name="table[action][foo][7]" value="link-text"  />'
            ],
            [
                'action' => 'C',
                '(Current) <input type="submit" class="" name="table[action][foo][7]" value="link-text" disabled="disabled" />'
            ],
            [
                'action' => 'D',
                '(Removed) <input type="submit" class="" name="table[action][foo][7]" value="link-text" disabled="disabled" />'
            ],
            [
                'action' => 'ABC',
                '<input type="submit" class="" name="table[action][foo][7]" value="link-text"  />'
            ]
        ];
    }
}
