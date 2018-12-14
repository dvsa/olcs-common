<?php

namespace OlcsTest\Service\Data\Search;

use Common\Service\Data\Search\SearchTypeManager;
use Common\Service\Data\Search\SearchTypeManagerFactory;
use CommonTest\Service\Data\Search\Asset\SearchType;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class SearchTypeManagerFactoryTest
 * @package OlcsTest\Service\Data\Search
 */
class SearchTypeManagerFactoryTest extends TestCase
{
    public function testCreateService()
    {
        $search = new SearchType();

        $serviceConfig = ['search' => ['services' => ['testService' => $search]]];

        $sut = new SearchTypeManagerFactory();

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('get')->with('Config')->andReturn($serviceConfig);

        $service = $sut->createService($mockSl);

        $this->assertInstanceOf(SearchTypeManager::class, $service);
        $this->assertEquals($search, $service->get('testService'));
    }
}
