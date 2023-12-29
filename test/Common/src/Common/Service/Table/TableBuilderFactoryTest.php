<?php

namespace CommonTest\Service\Table;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Common\Service\Table\TableBuilder;
use Common\Service\Table\TableBuilderFactory;
use Laminas\Mvc\I18n\Translator;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use LmcRbacMvc\Service\AuthorizationService;

class TableBuilderFactoryTest extends MockeryTestCase
{
    /**
     * Test createService
     */
    public function testInvoke(): void
    {
        $config = ['config1', 'config2'];

        $authService = m::mock(AuthorizationService::class);
        $translator = m::mock(Translator::class);
        $urlHelperService = m::mock(UrlHelperService::class);
        $formatterPluginManager = m::mock(FormatterPluginManager::class);

        $serviceLocator = m::mock(ServiceManager::class);

        $serviceLocator->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $serviceLocator->shouldReceive('get')
            ->with('LmcRbacMvc\Service\AuthorizationService')
            ->andReturn($authService);

        $serviceLocator->shouldReceive('get')
            ->with('translator')
            ->andReturn($translator);

        $serviceLocator->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($urlHelperService);

        $serviceLocator->shouldReceive('get')
            ->with(FormatterPluginManager::class)
            ->andReturn($formatterPluginManager);

        $tableFactory = new TableBuilderFactory();
        $tableBuilder = $tableFactory->__invoke($serviceLocator, TableBuilder::class);

        $this->assertTrue($tableBuilder instanceof TableBuilder);
    }
}
