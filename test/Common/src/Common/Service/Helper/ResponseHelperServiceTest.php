<?php

/**
 * Response Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\ResponseHelperService;

/**
 * Response Helper Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ResponseHelperServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ResponseHelperService();
    }

    public function testTableToCsv()
    {
        $response = m::mock('Zend\Http\Response');
        $table = m::mock('Common\Service\Table\TableBuilder');

        $table->shouldReceive('setContentType')
            ->with('csv')
            ->shouldReceive('removeColumn')
            ->with('action')
            ->shouldReceive('render')
            ->andReturn('body here');

        $response->shouldReceive('getHeaders')
            ->andReturn(
                m::mock()
                ->shouldReceive('addHeaderLine')
                ->with('Content-Type', 'text/csv')
                ->andReturnSelf()
                ->shouldReceive('addHeaderLine')
                ->with('Content-Disposition', 'attachment; filename="foo.csv"')
                ->andReturnSelf()
                ->shouldReceive('addHeaderLine')
                ->with('Content-Length', 9)
                ->andReturnSelf()
                ->getMock()
            )
            ->shouldReceive('setContent')
            ->with('body here');

        $result = $this->sut->tableToCsv($response, $table, 'foo');

        $this->assertSame($response, $result);
    }
}
