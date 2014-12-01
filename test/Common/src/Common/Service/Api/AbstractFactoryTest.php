<?php


namespace CommonTest\Service\Api;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Api\AbstractFactory;
use Mockery as m;

/**
 * Class AbstractFactoryTest
 * @package CommonTest\Service\Api
 */
class AbstractFactoryTest extends MockeryTestCase
{
    public function testCanCreateServiceWithName()
    {
        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');

        $sut = new AbstractFactory();
        $this->assertTrue($sut->canCreateServiceWithName($mockSl, '', 'Olcs\\RestService\\Backend\\Task'));
        $this->assertFalse($sut->canCreateServiceWithName($mockSl, '', 'Data\\Service\\Backend\\Task'));
    }

    public function testCreateService()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn($config);

        $sut = new AbstractFactory();
        $client = $sut->createServiceWithName($mockSl, '', 'Olcs\RestService\TaskType');
        $this->assertEquals('olcs-backend', $client->url->getHost());
        $this->assertEquals('/task-type', $client->url->getPath());

    }

    public function testCreateServiceInvalidMapping()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $mockSl = m::mock('Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn($config);

        $sut = new AbstractFactory();

        $passed = false;
        try {
            $sut->createServiceWithName($mockSl, '', 'Olcs\RestService\NoService\TaskType');
        } catch (\Exception $e) {
            if ($e->getMessage() == 'No endpoint defined for: NoService') {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown');
    }
}
