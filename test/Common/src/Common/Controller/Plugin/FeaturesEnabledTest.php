<?php

namespace CommonTest\Controller\Plugin;

use Common\Controller\Plugin\FeaturesEnabled;
use Common\Service\Cqrs\Query\QuerySender;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Laminas\Mvc\MvcEvent;

/**
 * FeaturesEnabled Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class FeaturesEnabledTest extends MockeryTestCase
{
    protected $action = 'action';

    protected $mvcEvent;

    protected $querySender;

    protected function setUp(): void
    {
        $this->mvcEvent = m::mock(MvcEvent::class);
        $this->mvcEvent->shouldReceive('getRouteMatch->getParam')->with('action')->andReturn($this->action);
        $this->querySender = m::mock(QuerySender::class);
    }

    public function testInvokeWithEmptyConfig(): void
    {
        $this->querySender->shouldNotReceive('featuresEnabled');
        $sut = new FeaturesEnabled($this->querySender);
        $this->assertEquals(false, $sut->__invoke([], $this->mvcEvent));
    }

    /**
     * @dataProvider dpTestInvoke
     */
    public function testInvoke($config, $checkedToggles, $expectedResult, $numChecks): void
    {
        $this->querySender->shouldReceive('featuresEnabled')->times($numChecks)->with($checkedToggles)->andReturn($expectedResult);
        $sut = new FeaturesEnabled($this->querySender);
        $this->assertEquals($expectedResult, $sut->__invoke($config, $this->mvcEvent));
    }

    public function dpTestInvoke()
    {
        $defaultConfig = ['default toggle 1', 'default toggle 2'];
        $actionConfig = ['action toggle 1', 'action toggle 2'];

        $bothConfigs = [
            'default' => $defaultConfig,
            $this->action => $actionConfig
        ];

        $defaultConfigOnly = [
            'default' => $defaultConfig
        ];

        //action config only
        $actionConfigOnly = [
            $this->action => $actionConfig
        ];

        //empty action config
        $emptyActionConfig = [
            $this->action => []
        ];

        //action config only
        $emptyDefaultConfig = [
            'default' => []
        ];

        return [
            [$bothConfigs, $actionConfig, true, 1],
            [$defaultConfigOnly, $defaultConfig, false, 1],
            [$actionConfigOnly, $actionConfig, true, 1],
            [$emptyActionConfig, [], true, 0],
            [$emptyDefaultConfig, [], true, 0],
        ];
    }
}
