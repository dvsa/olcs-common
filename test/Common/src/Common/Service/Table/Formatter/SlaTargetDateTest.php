<?php

/**
 * SlaTargetDate formatter test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\Date;
use Common\Service\Table\Formatter\SlaTargetDate;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\Http\Request;
use Laminas\Mvc\Router\Http\TreeRouteStack;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * SlaTargetDate formatter test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDateTest extends TestCase
{
    protected $urlHelper;
    protected $translator;
    protected $router;
    protected $request;
    protected $sut;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->router = m::mock(TreeRouteStack::class);
        $this->request = m::mock(Request::class);
        $this->sut = new SlaTargetDate($this->router, $this->request, $this->urlHelper, new Date());

        $this->mockRouteMatch = m::mock('\Laminas\Mvc\Router\RouteMatch');

        $this->router
            ->shouldReceive('match')
            ->with($this->request)
            ->andReturn($this->mockRouteMatch)
            ->getMock();
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @todo the Date formatter now appears to rely on global constants defined
     * in the Common\Module::modulesLoaded method which can cause this test to
     * fail :(
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    /**
     * Test the format method
     *
     * @group Formatters
     * @group SlaTargetDateFormatter
     *
     * @dataProvider provider
     */
    public function testFormat($data, $routeMatch, $expectedRoute, $expectedRouteParams, $expectedLink)
    {
        $this->mockRouteMatch
            ->shouldReceive('getMatchedRouteName')
            ->andReturn($routeMatch);

        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedRouteParams, [], true)
            ->andReturn('the_url');

        $this->assertEquals($expectedLink, $this->sut->format($data, []));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'case-documents-no-target-date' => [
                [
                    'id' => 201,
                    'agreedDate' => '2001-01-01'
                ],
                'case/documents',
                'case/documents/edit-sla',
                ['entityType' => 'document', 'entityId' => 201],
                '<a href="the_url" class="govuk-link js-modal-ajax">Not set</a> ',
            ],
            'case-documents-target-date-set' => [
                [
                    'id' => 201,
                    'agreedDate' => '2001-01-01',
                    'targetDate' => '2001-02-02',
                    'sentDate' => '2001-01-01'
                ],
                'case/documents',
                'case/documents/edit-sla',
                ['entityType' => 'document', 'entityId' => 201],
                '<a href="the_url" class="govuk-link js-modal-ajax">02/02/2001</a> <span class="status green">Pass</span>',
            ],
            'case-documents-not-set' => [
                [
                    'id' => 201,
                    'agreedDate' => '',
                    'targetDate' => '2001-02-02',
                    'sentDate' => '2001-01-01'
                ],
                'case/documents',
                'case/documents/add-sla',
                ['entityType' => 'document', 'entityId' => 201],
                '<a href="the_url" class="govuk-link js-modal-ajax">Not set</a>',
            ]
        ];
    }
}
