<?php

namespace CommonTest\Preference;

use Common\Preference\Language;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Http\Header\Cookie;
use Laminas\Http\Header\SetCookie;
use Laminas\Http\Request;
use Laminas\Http\Response;

class LanguageTest extends MockeryTestCase
{
    /**
     * @var Language
     */
    protected $sut;

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

    public function setUp(): void
    {
        $this->sut = new Language();

        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $this->sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

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

    public function testInvoke(): void
    {
        $cookie = m::mock();

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->__invoke($this->sm, SetCookie::class);

        $this->assertInstanceOf(SetCookie::class, $this->setCookie);

        $this->assertEquals('en', $this->setCookie->getValue());
    }

    public function testInvokeWithCookie(): void
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->__invoke($this->sm, SetCookie::class);

        $this->assertInstanceOf(SetCookie::class, $this->setCookie);

        $this->assertEquals('cy', $this->setCookie->getValue());
    }

    public function testSetPreferenceException()
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->__invoke($this->sm, SetCookie::class);

        $this->expectException('\Exception');

        $this->sut->setPreference('XX');
    }

    public function testSetPreference()
    {
        $cookie = m::mock(Cookie::class)->makePartial();
        $cookie['langPref'] = 'cy';

        $this->request->shouldReceive('getCookie')
            ->andReturn($cookie);

        $this->sut->__invoke($this->sm, SetCookie::class);

        $this->sut->setPreference('en');

        $this->assertEquals('en', $this->setCookie->getValue());
        $this->assertEquals('en', $this->sut->getPreference());
    }
}
