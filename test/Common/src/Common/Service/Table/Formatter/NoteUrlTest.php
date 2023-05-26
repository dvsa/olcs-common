<?php

/**
 * Note Url formatter test
 *
 * @author Alex.Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\NoteUrl;
use Laminas\Http\Request;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Note Url formatter test
 *
 * @author Alex.Peshkov <alex.peshkov@valtech.co.uk>
 */
class NoteUrlTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $sut;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->request = m::mock(Request::class);
        $this->sut = new NoteUrl($this->request, $this->urlHelper);
    }

    protected function tearDown(): void
    {
        m::close();
    }
    /**
     * Test the format method
     */
    public function testFormat()
    {
        $data = [
            'id' => 1,
            'createdOn' => '2015-01-01 10:10'
        ];

        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with(
                null,
                ['action' => 'edit', 'id' => $data['id']],
                ['query' => ['foo' => 'bar']],
                true
            )
            ->andReturn('the_url')
            ->getMock();

        $this->request
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

        $expectedLink = '<a class="govuk-link js-modal-ajax" href="the_url">'
            . (new \DateTime($data['createdOn']))->format(\DATE_FORMAT) . '</a>';

        $this->assertEquals($expectedLink, $this->sut->format($data, []));
    }
}
