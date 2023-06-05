<?php

namespace CommonTest\Service\Api;

use Common\Service\Api\AbstractFactory;
use Laminas\Authentication\Storage\Session;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Request;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class AbstractFactoryTest
 * @package CommonTest\Service\Api
 */
class AbstractFactoryTest extends MockeryTestCase
{
    /** @var AbstractFactory | m\MockInterface */
    protected $sut;

    /** @var m\MockInterface | ServiceLocatorInterface */
    protected $mockSl;

    /** @var m\MockInterface | Request */
    protected $mockRequest;

    /** @var m\MockInterface | Translator */
    protected $mockTranslator;

    /**
     * @var Laminas\Authentication\Storage\Session|m\LegacyMockInterface|m\MockInterface
     */
    private $mockSession;

    public function setUp(): void
    {
        $this->sut = new AbstractFactory();

        $this->mockSl = m::mock(ServiceLocatorInterface::class);

        $this->mockRequest = m::mock(Request::class);

        $this->mockTranslator = m::mock(Translator::class);

        $this->mockSession = m::mock(Session::class);
    }

    /**
     * @dataProvider dpTestCanCreate
     */
    public function testCanCreate($requestedName, $expect)
    {
        static::assertEquals($expect, $this->sut->canCreate($this->mockSl, $requestedName));
    }

    public function dpTestCanCreate()
    {
        return [
            [
                'requestedName' => 'Olcs\\RestService\\Backend\\Task',
                'expect' => true,
            ],
            [
                'requestedName' => 'Data\\Service\\Backend\\Task',
                'expect' => false,
            ],
        ];
    }

    /**
     * @dataProvider dpTestCanCreate
     * @todo OLCS-28149
     */
    public function testCanCreateServiceWithName($requestedName, $expect)
    {
        static::assertEquals($expect, $this->sut->canCreateServiceWithName($this->mockSl, '', $requestedName));
    }

    public function testCreateService()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $this->mockTranslator->shouldReceive('getLocale')->withNoArgs()->andReturn('en-ts');

        $this->mockRequest->shouldReceive('getCookie')->andReturn(new Cookie(['secureToken' => 'abad1dea']));

        $this->mockSession->shouldReceive('read')->andReturn(['AccessToken' => 'abc123']);

        $this->mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $this->mockSl->shouldReceive('get')->with('translator')->andReturn($this->mockTranslator);
        $this->mockSl->shouldReceive('get')->with('Request')->andReturn($this->mockRequest);
        $this->mockSl->shouldReceive('get')->with(Session::class)->andReturn($this->mockSession);

        $client = ($this->sut)($this->mockSl, 'Olcs\RestService\TaskType');
        $this->assertEquals('olcs-backend', $client->url->getHost());
        $this->assertEquals('/task-type', $client->url->getPath());
        $this->assertEquals('en-ts', $client->getLanguage());

        // TODO OLCS-28149
        $client = $this->sut->createServiceWithName($this->mockSl, '', 'Olcs\RestService\TaskType');
        $this->assertEquals('olcs-backend', $client->url->getHost());
        $this->assertEquals('/task-type', $client->url->getPath());
        $this->assertEquals('en-ts', $client->getLanguage());
    }

    public function testCreateServiceInvalidMapping()
    {
        $config['service_api_mapping']['endpoints']['backend'] = 'http://olcs-backend';

        $this->mockSl->shouldReceive('get')->with('Config')->andReturn($config);

        $passed = false;
        try {
            ($this->sut)($this->mockSl, 'Olcs\RestService\NoService\TaskType');
        } catch (\Exception $e) {
            if ($e->getMessage() == 'No endpoint defined for: NoService') {
                $passed = true;
            }
        }
        $this->assertTrue($passed, 'Expected exception not thrown');

        // TODO OLCS-28149
        $passed = false;
        try {
            $this->sut->createServiceWithName($this->mockSl, '', 'Olcs\RestService\NoService\TaskType');
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

        $this->mockTranslator->shouldReceive('getLocale')->withNoArgs()->andReturn('en-ts');

        $this->mockRequest->shouldReceive('getCookie')->andReturn(new Cookie(['secureToken' => 'abad1dea']));

        $this->mockSession->shouldReceive('read')->andReturn(['AccessToken' => 'abc123']);

        $this->mockSl->shouldReceive('get')->with('Config')->andReturn($config);
        $this->mockSl->shouldReceive('get')->with('translator')->andReturn($this->mockTranslator);
        $this->mockSl->shouldReceive('get')->with('Request')->andReturn($this->mockRequest);
        $this->mockSl->shouldReceive('get')->with(Session::class)->andReturn($this->mockSession);

        $client = ($this->sut)($this->mockSl, 'myapi\\some-resource');
        $this->assertEquals('external-api', $client->url->getHost());
        $this->assertEquals('/some-resource', $client->url->getPath());
        $this->assertEquals('en-ts', $client->getLanguage());

        // TODO OLCS-28149
        $client = $this->sut->createServiceWithName($this->mockSl, '', 'myapi\\some-resource');
        $this->assertEquals('external-api', $client->url->getHost());
        $this->assertEquals('/some-resource', $client->url->getPath());
        $this->assertEquals('en-ts', $client->getLanguage());
    }
}
