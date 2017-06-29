<?php

namespace CommonTest;

use Common\Module;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\Application;
use Zend\Validator\Csrf;

/**
 * @covers \Common\Module
 */
class ModuleTest extends MockeryTestCase
{
    private static $cfg = [
        'csrf' => [
            'timeout' => 9999,
            'whitelist' => [
                'unit_whitelisted_path',
            ],
        ],
    ];

    /** @var Module */
    protected $sut;

    /** @var  m\MockInterface */
    private $mockReq;
    /** @var  \Zend\Mvc\MvcEvent | m\MockInterface */
    private $mockEvent;
    /** @var  \Zend\ServiceManager\ServiceLocatorInterface | m\MockInterface */
    private $mockSm;
    /** @var  m\MockInterface */
    private $mockApp;

    public function setUp()
    {
        $this->sut = new Module();

        $this->mockReq = m::mock(\Zend\Http\Request::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->mockSm->shouldReceive('get')->with('config')->andReturn(self::$cfg);

        $this->mockApp = m::mock(Application::class);
        $this->mockApp->shouldReceive('getServiceManager')->andReturn($this->mockSm);

        $this->mockEvent = m::mock(\Zend\Mvc\MvcEvent::class);
        $this->mockEvent
            ->shouldReceive('getRequest')->andReturn($this->mockReq);
    }

    public function testValidateCsrfTokenNotPost()
    {
        $this->mockReq->shouldReceive('isPost')->andReturn(false);

        $this->mockEvent->shouldReceive('getApplication')->never();

        static::assertNull($this->sut->validateCsrfToken($this->mockEvent));
    }

    public function testValidateCsrfTokenWitelisted()
    {
        $this->mockReq
            ->shouldReceive('isPost')->andReturn(true)
            ->shouldReceive('getPost')->never();
        $this->mockReq->shouldReceive('getUri->getPath')->andReturn('unit_whitelisted_path');

        $this->mockEvent->shouldReceive('getApplication')->once()->andReturn($this->mockApp);

        static::assertNull($this->sut->validateCsrfToken($this->mockEvent));
    }

    public function testValidateCsrfTokenEmptyPost()
    {
        $this->mockReq->shouldReceive('isPost')->andReturn(false);
        $this->mockReq->shouldReceive('getPost->count')->never();

        $this->mockEvent->shouldReceive('getApplication')->never();

        static::assertNull($this->sut->validateCsrfToken($this->mockEvent));
    }

    public function testValidateCsrfTokenValid()
    {
        $validator = new Csrf(['name' => 'security']);
        $hash = $validator->getHash();

        $mockParams = m::mock(\Zend\Stdlib\Parameters::class)
            ->shouldReceive('count')->andReturn(1)
            ->getMock();

        $this->mockReq->shouldReceive('getUri->getPath')->andReturn('unit_NOT_whitelisted_path');
        $this->mockReq
            ->shouldReceive('isPost')->once()->andReturn(true)
            ->shouldReceive('getPost')->once()->withNoArgs()->andReturn($mockParams)
            ->shouldReceive('getPost')->once()->with('security')->andReturn($hash);

        $this->mockEvent->shouldReceive('getApplication')->once()->andReturn($this->mockApp);

        static::assertNull($this->sut->validateCsrfToken($this->mockEvent));
    }

    public function testValidateCsrfTokenNotValid()
    {
        $mockFlashHlp = m::mock(\Common\Service\Helper\FlashMessengerHelperService::class);
        $mockFlashHlp->shouldReceive('addErrorMessage')->once()->with('csrf-message');
        $this->mockSm->shouldReceive('get')->with('Helper\FlashMessenger')->andReturn($mockFlashHlp);

        $mockParams = m::mock(\Zend\Stdlib\Parameters::class)
            ->shouldReceive('count')->andReturn(1)
            ->getMock();

        $this->mockReq->shouldReceive('getUri->getPath')->andReturn('unit_NOT_whitelisted_host');
        $this->mockReq
            ->shouldReceive('isPost')->once()->andReturn(true)
            ->shouldReceive('getPost')->once()->withNoArgs()->andReturn($mockParams)
            ->shouldReceive('getPost')->once()->with('security')->andReturn('NOT VALID HASH')
            ->shouldReceive('setMethod')->once()->with(\Zend\Http\Request::METHOD_GET);

        $this->mockEvent->shouldReceive('getApplication')->once()->andReturn($this->mockApp);
        $this->mockEvent
            ->shouldReceive('getResponse->getHeaders->addHeaderLine')
            ->once()
            ->with('X-CSRF-error', '1');

        static::assertNull($this->sut->validateCsrfToken($this->mockEvent));
    }
}
