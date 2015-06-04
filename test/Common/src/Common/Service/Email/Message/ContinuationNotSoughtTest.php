<?php

/**
 * ContinuationNotSought service test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace CommonTest\Service\Email\Message;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Email\Message\ContinuationNotSought;
use CommonTest\Bootstrap;

/**
 * ContinuationNotSought service test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationNotSoughtTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new ContinuationNotSought();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testSend()
    {
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);

        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $mockViewHelperManager = m::mock();
        $this->sm->setService('viewhelpermanager', $mockViewHelperManager);

        $mockViewHelperManager->shouldReceive('get')->with('dateFormat')->once()->andReturn(
            function () {
                return 'DATETIME';
            }
        );

        $mockViewRenderer = m::mock();
        $this->sm->setService('ViewRenderer', $mockViewRenderer);

        $mockEmail = m::mock();
        $this->sm->setService('Email', $mockEmail);

        $mockSystemParameter = m::mock();
        $this->sm->setService('Entity\SystemParameter', $mockSystemParameter);

        $mockTranslationHelper = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslationHelper);

        $licences = [
            'Results' => [],
        ];

        $mockDateHelper->shouldReceive('getDate')->with(\DateTime::W3C)->once()->andReturn('2015-04-30T17:18:38+01:00');
        $mockLicenceEntityService->shouldReceive('getWhereContinuationNotSought')
            ->with('2015-03-30T17:18:38+01:00', '2015-04-30T17:18:38+01:00')
            ->once()
            ->andReturn($licences);

        $mockSystemParameter->shouldReceive('getValue')->with('CNS_EMAIL_LIST')->once()->andReturn('andy@example.com');
        $mockTranslationHelper->shouldReceive('translateReplace')
            ->with('email.cns.subject', ['DATETIME', 'DATETIME'])
            ->once()
            ->andReturn('SUBJECT');

        $mockViewRenderer->shouldReceive('render')->twice()->andReturn('RENDERED');

        $mockEmail->shouldReceive('sendEmail')
            ->with(
                'donotreply@otc.gsi.gov.uk',
                'OLBS eCommerce/LIC/VIA',
                'andy@example.com',
                'SUBJECT',
                'RENDERED'
            )->once();

        $this->sut->send();
    }
}
