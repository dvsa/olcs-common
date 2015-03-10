<?php

/**
 * Payment Submission Form Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Helper;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\PaymentSubmissionFormHelperService as Sut;
use CommonTest\Bootstrap;

/**
 * Payment Submission Form Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentSubmissionFormHelperServiceTest extends MockeryTestCase
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

    public function testUpdatePaymentSubmissonFormWithFees()
    {
        $applicationId = 69;
        $applicationFee = ['id' => 1, 'amount' => 1234.56];
        $interimFee = ['id' => 2, 'amount' => 123.45];
        $actionUrl = 'actionUrl';

        $form = m::mock('\Zend\Form\Form')
            ->shouldReceive('get')
                ->with('amount')
                ->once()
                ->andReturn(
                    m::mock()
                        ->shouldReceive('setValue')
                        ->with('HTML VALUE')
                    ->getMock()
                )
            ->shouldReceive('setAttribute')
                ->once()
                ->with('action', 'actionUrl')
                ->andReturnSelf()
            ->getMock();

        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sm->setService(
            'Processing\Application',
            m::mock()
                ->shouldReceive('getApplicationFee')
                    ->once()
                    ->with($applicationId)
                    ->andReturn($applicationFee)
                ->shouldReceive('getInterimFee')
                    ->once()
                    ->with($applicationId)
                    ->andReturn($interimFee)
                ->getMock()
        );

        $this->sm->setService(
            'Helper\Translation',
            m::mock()
                ->shouldReceive('translateReplace')
                    ->once()
                    ->with('application.payment-submission.amount.value', array('1,358.01'))
                    ->andReturn('HTML VALUE')
                ->getMock()
        );

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, $applicationId, true, true);
    }

    public function testUpdatePaymentSubmissonFormWithNoFeesAndIncomplete()
    {
        $applicationId = 69;
        $actionUrl = 'actionUrl';

        $form = m::mock('\Zend\Form\Form')
            ->shouldReceive('get')
                ->with('submitPay')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('setLabel')
                        ->once()
                        ->with('submit-application.button')
                    ->getMock()
                )
            ->getMock();

        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        $this->sm->setService(
            'Processing\Application',
            m::mock()
                ->shouldReceive('getApplicationFee')
                    ->once()
                    ->with($applicationId)
                    ->andReturn(null)
                ->shouldReceive('getInterimFee')
                    ->once()
                    ->with($applicationId)
                    ->andReturn(null)
                ->getMock()
        );

        // assert fee amount is removed
        $formHelper->shouldReceive('remove')->once()->with($form, 'amount');

        // assert button is disabled
        $formHelper->shouldReceive('disableElement')->once()->with($form, 'submitPay');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, $applicationId, true, false);
    }

    public function testUpdatePaymentSubmissonFormAlreadySubmitted()
    {
        $form = m::mock('\Zend\Form\Form');
        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        // assert button and fee amount are removed
        $formHelper->shouldReceive('remove')->once()->with($form, 'submitPay');
        $formHelper->shouldReceive('remove')->once()->with($form, 'amount');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, '', null, false, false);
    }
}
