<?php

/**
 * Language Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Preference;

use Common\Preference\Language;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Http\Header\Cookie;
use Zend\Http\Header\SetCookie;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Language Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LanguageTest extends MockeryTestCase
{
    /**
     * @var Language
     */
    protected $sut;

    /**
     * @var ServiceLocatorInterface
     */
    protected $sm;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var SetCookie
     */
    protected $setCookie;

    public function setUp()
    {
        $this->sut = new Language();

        $this->sm = Bootstrap::getServiceManager();
        $this->request = m::mock(Request::class);
        $this->response = m::mock(Response::class);

        $this->response->shouldReceive('getHeaders->addHeader')
            ->once()
            ->with(m::type(SetCookie::class))
            ->andReturnUsing(
                function ($cookie) {
                    $this->setCookie = $cookie;
                }
            );

        $this->sm->setService('Request', $this->request);
        $this->sm->setService('Response', $this->response);
    }

    public function testCreateService()
    {
        $cookie = m::mock();

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->createService($this->sm);

        $this->assertInstanceOf(SetCookie::class, $this->setCookie);

        $this->assertEquals('en', $this->setCookie->getValue());
    }

    public function testCreateServiceWithCookie()
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->createService($this->sm);

        $this->assertInstanceOf(SetCookie::class, $this->setCookie);

        $this->assertEquals('cy', $this->setCookie->getValue());
    }

    public function testSetPreferenceException()
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->createService($this->sm);

        $this->setExpectedException('\Exception');

        $this->sut->setPreference('XX');
    }

    public function testSetPreference()
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->createService($this->sm);

        $this->sut->setPreference('en');

        $this->assertEquals('en', $this->setCookie->getValue());
        $this->assertEquals('en', $this->sut->getPreference());
    }
}
