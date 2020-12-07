<?php


namespace CommonTest\Service\Api;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Api\AbstractFactory;
use Mockery as m;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Request;

/**
 * Class AbstractFactoryTest
 * @package CommonTest\Service\Api
 */
class AbstractFactoryTest extends MockeryTestCase
{
    public function testCanCreateServiceWithName()
    {
        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');

        $sut = new AbstractFactory();
        $this->assertTrue($sut->canCreateServiceWithName($mockSl, '', 'Olcs\\RestService\\Backend\\Task'));
        $this->assertFalse($sut->canCreateServiceWithName($mockSl, '', 'Data\\Service\\Backend\\Task'));
    }

    public function testCreateService()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $translator = m::mock('stdClass');
        $translator->shouldReceive('getLocale')->withNoArgs()->andReturn('en-ts');
        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn(new Cookie(['secureToken' => 'abad1dea']));

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('getServiceLocator->get')->with('translator')->andReturn($translator);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Request')->andReturn($mockRequest);

        $sut = new AbstractFactory();
        $client = $sut->createServiceWithName($mockSl, '', 'Olcs\RestService\TaskType');
        $this->assertEquals('olcs-backend', $client->url->getHost());
        $this->assertEquals('/task-type', $client->url->getPath());

        $this->assertEquals('en-ts', $client->getLanguage());
    }

    public function testCreateServiceInvalidMapping()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
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

    public function testCreateServiceAdditionalEndpointConfig()
    {
        $config['service_api_mapping']['endpoints']['myapi'] = [
            'url' => 'https://external-api',
                'options' => [
                    'sslcapath' => '/etc/ssl/certs',
                    'sslverifypeer' => false,
                ],
                'auth' => [
                    'username' => 'foo',
                    'password' => 'bar',
                ],
        ];

        $translator = m::mock('stdClass');
        $translator->shouldReceive('getLocale')->withNoArgs()->andReturn('en-ts');

        $mockRequest = m::mock(Request::class);
        $mockRequest->shouldReceive('getCookie')->andReturn(new Cookie(['secureToken' => 'abad1dea']));

        $mockSl = m::mock('Laminas\ServiceManager\ServiceLocatorInterface');
        $mockSl->shouldReceive('getServiceLocator->get')->with('Config')->andReturn($config);
        $mockSl->shouldReceive('getServiceLocator->get')->with('translator')->andReturn($translator);
        $mockSl->shouldReceive('getServiceLocator->get')->with('Request')->andReturn($mockRequest);

        $sut = new AbstractFactory();
        $client = $sut->createServiceWithName($mockSl, '', 'myapi\\some-resource');
        $this->assertEquals('external-api', $client->url->getHost());
        $this->assertEquals('/some-resource', $client->url->getPath());

        $this->assertEquals('en-ts', $client->getLanguage());
    }
}
