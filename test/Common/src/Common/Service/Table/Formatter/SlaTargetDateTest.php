<?php

/**
 * SlaTargetDate formatter test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\SlaTargetDate;
use CommonTest\Bootstrap;
use Mockery as m;

/**
 * SlaTargetDate formatter test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTargetDateTest extends \PHPUnit_Framework_TestCase
{
    protected $sm;

    protected $mockRouteMatch;

    protected $mockUrlHelper;

    /**
     * @todo the Date formatter now appears to rely on global constants defined
     * in the Common\Module::modulesLoaded method which can cause this test to
     * fail :(
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        if (!defined('DATE_FORMAT')) {
            define('DATE_FORMAT', 'd/m/Y');
        }
    }

    public function setUp()
    {
        parent::setUp();

        $this->sm = Bootstrap::getServiceManager();

        $this->mockRouteMatch = m::mock('\Zend\Mvc\Router\RouteMatch');
        $this->mockUrlHelper = m::mock();
        $mockRequest = m::mock('\Zend\Stdlib\RequestInterface');
        $mockRouter = m::mock()
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn($this->mockRouteMatch)
            ->getMock();

        $this->sm->setService('router', $mockRouter);
        $this->sm->setService('request', $mockRequest);
        $this->sm->setService('Helper\Url', $this->mockUrlHelper);
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

        $this->mockUrlHelper
            ->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedRouteParams, [], true)
            ->andReturn('the_url');

        $this->assertEquals($expectedLink, SlaTargetDate::format($data, [], $this->sm));
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
                '<a href="the_url" class="js-modal-ajax">Not set</a> ',
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
                '<a href="the_url" class="js-modal-ajax">02/02/2001</a> <span class="status green">Pass</span>',
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
                '<a href="the_url" class="js-modal-ajax">Not set</a>',
            ]
        ];
    }
}
