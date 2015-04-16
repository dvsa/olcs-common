<?php

namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DashboardTmActionLink;
use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Class DashboardTmActionLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class DashboardTmActionLinkTest extends MockeryTestCase
{
    private $sut;

    /* @var \Mockery\MockInterface */
    private $sm;

    /* @var \Mockery\MockInterface */
    private $mockViewHelper;

    public function setUp()
    {
        $this->sut = new DashboardTmActionLink();

        $this->mockViewHelper = m::mock();

        $this->sm = m::mock('StdClass');
        $this->sm->shouldReceive('get->fromRoute')
            ->once()
            ->andReturn('http://url.com');
    }

    public function dataProviderFormat()
    {
        return [
            ['Provide details', TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE],
            ['Provide details', TransportManagerApplicationEntityService::STATUS_INCOMPLETE],
            ['View details', TransportManagerApplicationEntityService::STATUS_OPERATOR_SIGNED],
            ['View details', TransportManagerApplicationEntityService::STATUS_POSTAL_APPLICATION],
            ['View details', TransportManagerApplicationEntityService::STATUS_TM_SIGNED],
        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat($expectedLinkText, $status)
    {
        $data = [
            'applicationId' => 323,
            'transportManagerApplicationStatus' => [
                'id' => $status,
                'description' => 'FooBar',
            ],
            'transportManagerApplicationId' => 12
        ];
        $column = [];
        $expected = '<b><a href="http://url.com">'. $expectedLinkText .'</a></b>';

        $this->assertEquals($expected, $this->sut->format($data, $column, $this->sm));
    }
}
