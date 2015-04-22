<?php

/**
 * Email service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Email;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Email\Email as Sut;
use CommonTest\Bootstrap;

/**
 * Email service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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
     * Test send inspection request email method
     */
    public function testSendEmail()
    {
        // stub data
        $from    = 'from@example.com';
        $to      = 'to@example.com';
        $subject = 'test';
        $body    = 'foo';

        // mocks
        $restHelperMock = m::mock();
        $this->sm->setService('Helper\Rest', $restHelperMock);

        // expectations
        $restHelperMock
            ->shouldReceive('sendPost')
            ->with(
                "email\\",
                [
                    'from'    => $from,
                    'to'      => $to,
                    'subject' => $subject,
                    'body'    => $body,
                    'html'    => false,
                ]
            )
            ->once()
            ->andReturn(true);

        // assertions
        $this->assertTrue($this->sut->sendEmail($from, $to, $subject, $body));
    }

    public function testSendHtmlEmail()
    {
        // stub data
        $from    = 'from@example.com';
        $to      = 'to@example.com';
        $subject = 'test';
        $body    = 'foo';
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
                    'from'    => $from,
                    'to'      => $to,
                    'subject' => $subject,
                    'body'    => $body,
                    'html'    => $html,
                ]
            )
            ->once()
            ->andReturn(true);

        // assertions
        $this->assertTrue($this->sut->sendEmail($from, $to, $subject, $body, $html));
    }
}
