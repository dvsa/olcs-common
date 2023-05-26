<?php

/**
 * TableBuilderFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\FormatterPluginManager;
use Common\Service\Table\TableBuilderFactory;
use Laminas\Mvc\I18n\Translator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * TableBuilderFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class TableBuilderFactoryTest extends MockeryTestCase
{
    /**
     * Test createService
     */
    public function testCreateService()
    {
        $config = ['config1', 'config2'];

        $authService = m::mock(AuthorizationService::class);
        $translator = m::mock(Translator::class);
        $urlHelperService = m::mock(UrlHelperService::class);
        $formatterPluginManager = m::mock(FormatterPluginManager::class);

        $serviceLocator = m::mock(\Laminas\ServiceManager\ServiceManager::class);

        $serviceLocator->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $serviceLocator->shouldReceive('get')
            ->with('ZfcRbac\Service\AuthorizationService')
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
        $tableBuilder = $tableFactory->createService($serviceLocator);

        $this->assertTrue($tableBuilder instanceof \Common\Service\Table\TableBuilder);
    }
}
