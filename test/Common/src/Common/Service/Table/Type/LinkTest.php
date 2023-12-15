<?php

/**
 * Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Type;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Type\Link;
use CommonTest\Bootstrap;
use Common\Service\Helper\StackHelperService;

/**
 * Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LinkTest extends MockeryTestCase
{
    protected $sut;
    protected $table;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $this->sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

        $this->table = m::mock();
        $this->table->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);

        $this->sut = new Link($this->table);
    }

    public function testRender()
    {
        $data = [
            'some' => [
                'id' => 123
            ]
        ];
        $column = [
            'route' => 'foo',
            'params' => [
                'id' => '{some->id}'
            ]
        ];
        $formattedContent = '<a href="[LINK]">Some Link</a>';
        $expected = '<a href="URL">Some Link</a>';

        // Mocks
        $urlHelper = m::mock();
        $this->sm->setService('Helper\Url', $urlHelper);
        // @NOTE We use the real stack helper here, as it's a useful component test
        // and is only a tiny utility class that is also fully tested elsewhere
        $this->sm->setService('Helper\Stack', new StackHelperService());

        $urlHelper->shouldReceive('fromRoute')
            ->once()
            ->with('foo', ['id' => 123])
            ->andReturn('URL');

        $this->assertEquals($expected, $this->sut->render($data, $column, $formattedContent));
    }
}
