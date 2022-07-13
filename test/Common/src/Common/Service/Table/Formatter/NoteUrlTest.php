<?php

/**
 * Note Url formatter test
 *
 * @author Alex.Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\NoteUrl;
use CommonTest\Bootstrap;

/**
 * Note Url formatter test
 *
 * @author Alex.Peshkov <alex.peshkov@valtech.co.uk>
 */
class NoteUrlTest extends MockeryTestCase
{
    /**
     * Test the format method
     */
    public function testFormat()
    {
        $data = [
            'id' => 1,
            'createdOn' => '2015-01-01 10:10'
        ];
        $sm = Bootstrap::getServiceManager();

        $mockUrlHelper = m::mock()
            ->shouldReceive('fromRoute')
            ->with(
                null,
                ['action' => 'edit', 'id' => $data['id']],
                ['query' => ['foo' => 'bar']],
                true
            )
            ->andReturn('the_url')
            ->getMock();

        $mockRequest = m::mock('\Laminas\Stdlib\RequestInterface')
            ->shouldReceive('getQuery')
            ->andReturn(
                m::mock()
                    ->shouldReceive('toArray')
                    ->once()
                    ->andReturn(['foo' => 'bar'])
                    ->getMock()
            )
            ->once()
            ->getMock();

        $sm->setService('request', $mockRequest);
        $sm->setService('Helper\Url', $mockUrlHelper);

        $expectedLink = '<a class="govuk-link js-modal-ajax" href="the_url">'
            . (new \DateTime($data['createdOn']))->format(\DATE_FORMAT) . '</a>';

        $this->assertEquals($expectedLink, NoteUrl::format($data, [], $sm));
    }
}
