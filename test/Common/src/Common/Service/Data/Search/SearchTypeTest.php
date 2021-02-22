<?php

namespace CommonTest\Service\Data\Search;

use Common\Data\Object\Search\Application;
use Common\Data\Object\Search\Licence;
use Common\Data\Object\Search\User;
use Common\RefData;
use Common\Service\Data\Search\SearchType;
use Common\Service\Data\Search\SearchTypeManager;
use Common\Service\NavigationFactory;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use ZfcRbac\Service\RoleService;

/**
 * Class SearchTypeTest
 * @package CommonTest\Service\Data\Search
 */
class SearchTypeTest extends TestCase
{
    protected function getMockSearchTypeManager()
    {
        $servicesArray = [
            'factories' => [
                'licence'
            ],
            'invokableClasses' => [
                'application',
                'user'
            ]
        ];

        $mockStm = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockStm->shouldReceive('getRegisteredServices')->andReturn($servicesArray);
        $mockStm->shouldReceive('get')->with('application')->andReturn(new Application());
        $mockStm->shouldReceive('get')->with('licence')->andReturn(new Licence());
        $mockStm->shouldReceive('get')->with('user')->andReturn(new User());

        return $mockStm;
    }

    public function testGetNavigation()
    {
        $matcher = function ($item) {
            return (is_array($item) && count($item) == 2);
        };

        $mockNavFactory = m::mock(NavigationFactory::class);
        $mockNavFactory->shouldReceive('getNavigation')
            ->with(m::on($matcher))
            ->andReturn('navigation');

        $mockRoleService = $this->getMockRoleService('false', [RefData::ROLE_INTERNAL_LIMITED_READ_ONLY]);

        $sut = new SearchType();
        $sut->setSearchTypeManager($this->getMockSearchTypeManager());
        $sut->setNavigationFactory($mockNavFactory);
        $sut->setRoleService($mockRoleService);

        $this->assertEquals('navigation', $sut->getNavigation());
    }

    public function testFetchListOptions()
    {
        $mockRoleService = $this->getMockRoleService(false, [RefData::ROLE_INTERNAL_LIMITED_READ_ONLY]);

        $sut = new SearchType();
        $sut->setSearchTypeManager($this->getMockSearchTypeManager());
        $sut->setRoleService($mockRoleService);
        $options = $sut->fetchListOptions(null);


        $this->assertCount(3, $options);
    }

    public function testFetchListOptionsForLimitedReadOnly()
    {
        $mockRoleService = $this->getMockRoleService(true, [RefData::ROLE_INTERNAL_LIMITED_READ_ONLY]);

        $sut = new SearchType();
        $sut->setSearchTypeManager($this->getMockSearchTypeManager());
        $sut->setRoleService($mockRoleService);
        $options = $sut->fetchListOptions(null);


        $this->assertCount(2, $options);
    }

    public function testCreateService()
    {
        $mockStm = $this->getMockSearchTypeManager();
        $mockNav = m::mock(NavigationFactory::class);
        $mockRoleService = m::mock(RoleService::class);

        $mockSl = m::mock(ServiceLocatorInterface::class);
        $mockSl->shouldReceive('get')->with(SearchTypeManager::class)->andReturn($mockStm);
        $mockSl->shouldReceive('get')->with('NavigationFactory')->andReturn($mockNav);
        $mockSl->shouldReceive('get')->with(RoleService::class)->andReturn($mockRoleService);

        $sut = new SearchType();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(SearchType::class, $service);
        $this->assertSame($mockNav, $service->getNavigationFactory());
        $this->assertSame($mockStm, $service->getSearchTypeManager());
        $this->assertSame($mockRoleService, $service->getRoleService());
    }

    protected function getMockRoleService(bool $match, array $roles)
    {
        $mockRoleService = m::mock(RoleService::class);
        $mockRoleService->shouldReceive('matchIdentityRoles')
            ->with($roles)
            ->andReturn($match);

        return $mockRoleService;
    }
}
