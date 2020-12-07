<?php

/**
 * TableBuilderFactory Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilderFactory;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

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
        $serviceLocator = m::mock(\Laminas\ServiceManager\ServiceManager::class);

        $serviceLocator->shouldReceive('get')
            ->with('Config')
            ->andReturn('config');

        $serviceLocator->shouldReceive('get')
            ->with('ZfcRbac\Service\AuthorizationService')
            ->andReturn('auth');

        $serviceLocator->shouldReceive('get')
            ->with('translator')
            ->andReturn('translator');

        $tableFactory = new TableBuilderFactory();

        $tableBuilder = $tableFactory->createService($serviceLocator);

        $this->assertTrue($tableBuilder instanceof \Common\Service\Table\TableBuilder);
        $this->assertSame($tableBuilder->getTranslator(), 'translator');
        $this->assertSame($tableBuilder->getAuthService(), 'auth');
    }
}
