<?php

/**
 * TradingNames Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\TradingNames;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Cask Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TradingNamesTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new TradingNames();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testProcess()
    {
        // Params
        $params = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [
                'foo',
                'bar',
                ''
            ]
        ];

        $saveData = [
            'orgId' => 111,
            'licenceId' => 222,
            'tradingNames' => [
                'foo',
                'bar'
            ]
        ];

        // Mocks
        $tradingNamesRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('TradingNames', $tradingNamesRule);

        $mockOrganisation = m::mock();
        $mockTradingNames = m::mock();
        $this->sm->setService('Entity\Organisation', $mockOrganisation);
        $this->sm->setService('Entity\TradingNames', $mockTradingNames);

        // Expectations
        $tradingNamesRule->shouldReceive('filter')
            ->with(['foo', 'bar', ''])
            ->andReturn(['foo', 'bar'])
            ->shouldReceive('validate')
            ->with(['foo', 'bar'], 111, 222)
            ->andReturn($saveData);

        $mockOrganisation->shouldReceive('hasChangedTradingNames')
            ->with(111, ['foo', 'bar'])
            ->andReturn(true);

        $mockTradingNames->shouldReceive('save')
            ->with($saveData);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_PERSIST_SUCCESS, $response->getType());
        $this->assertEquals(['hasChanged' => true], $response->getData());
    }
}
