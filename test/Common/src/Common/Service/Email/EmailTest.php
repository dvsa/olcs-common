<?php

/**
 * Email service test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Email;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Email\Email as Sut;
use CommonTest\Bootstrap;

/**
 * Email service test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class EmailTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new Sut();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test send plain text email
     */
    public function testSendEmail()
    {
        // stub data
        $fromEmail = 'from@example.com';
        $fromName  = 'Fred Smith';
        $to        = 'to@example.com';
        $subject   = 'test';
        $body      = 'foo';

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        // expectations
        $restHelperMock
            ->shouldReceive('sendPost')
            ->with(
                "email\\",
                [
                    'fromEmail' => $fromEmail,
                    'fromName'  => $fromName,
                    'to'        => $to,
                    'subject'   => $subject,
                    'body'      => $body,
                    'html'      => false,
                ]
            )
            ->once()
            ->andReturn(true);

        // assertions
        $this->assertTrue($this->sut->sendEmail($fromEmail, $fromName, $to, $subject, $body, false));
    }

    public function testSendHtmlEmail()
    {
        // stub data
        $fromEmail = 'from@example.com';
        $fromName  = 'Fred Smith';
        $to        = 'to@example.com';
        $subject   = 'test';
        $body      = 'foo';
        $html    = '1';

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        // expectations
        $restHelperMock
            ->shouldReceive('sendPost')
            ->with(
                "email\\",
                [
                    'fromEmail' => $fromEmail,
                    'fromName'  => $fromName,
                    'to'        => $to,
                    'subject'   => $subject,
                    'body'      => $body,
                    'html'      => true,
                ]
            )
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->sut->sendEmail($fromEmail, $fromName, $to, $subject, $body, $html));
    }

    public function testSendTemplate()
    {
        // stub data
        $fromEmail = 'from@example.com';
        $fromName  = 'Fred Smith';
        $to        = 'to@example.com';
        $subject   = 'test';
        $view      = 'foo';
        $params    = [1, 2];

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        $this->sm->setService(
            'Helper\Translation',
            m::mock()
            ->shouldReceive('translateReplace')
            ->with($view, $params, true)
            ->andReturn('content here')
            ->shouldReceive('translate')
            ->with('test', true)
            ->andReturn('tset')
            ->getMock()
        );

        $this->sm->setService(
            'ViewRenderer',
            m::mock()
            ->shouldReceive('render')
            ->with(m::type('\Zend\View\Model\ViewModel'))
            ->andReturn('body here')
            ->getMock()
        );

        // expectations
        $restHelperMock
            ->shouldReceive('sendPost')
            ->with(
                "email\\",
                [
                    'fromEmail' => $fromEmail,
                    'fromName'  => $fromName,
                    'to'        => $to,
                    'subject'   => 'tset',
                    'body'      => 'body here',
                    'html'      => true,
                ]
            )
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->sut->sendTemplate(true, $fromEmail, $fromName, $to, $subject, $view, $params));
    }

    public function testSendEmailWithNoFromDetails()
    {
        // stub data
        $fromEmail = null;
        $fromName  = null;
        $to        = 'to@example.com';
        $subject   = 'test';
        $body      = 'foo';

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        $this->sm->setService(
            'config',
            [
                'email' => [
                    'default' => [
                        'from_address' => 'test@from.com',
                        'from_name' => 'Test Name'
                    ]
                ]
            ]
        );

        // expectations
        $restHelperMock
            ->shouldReceive('sendPost')
            ->with(
                "email\\",
                [
                    'fromEmail' => 'test@from.com',
                    'fromName'  => 'Test Name',
                    'to'        => $to,
                    'subject'   => $subject,
                    'body'      => $body,
                    'html'      => false,
                ]
            )
            ->once()
            ->andReturn(true);

        // assertions
        $this->assertTrue($this->sut->sendEmail($fromEmail, $fromName, $to, $subject, $body, false));
    }
}
