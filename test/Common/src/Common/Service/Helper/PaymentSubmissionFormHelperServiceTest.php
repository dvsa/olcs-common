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

    public function testUpdatePaymentSubmissonFormWithFee()
    {
        $fee = ['id' => 1, 'amount' => 1234.56];
        $actionUrl = 'actionUrl';

        $form = m::mock('\Zend\Form\Form')
            ->shouldReceive('get')
                ->with('amount')
                ->once()
                ->andReturn(
                    m::mock()
                        ->shouldReceive('setTokens')
                        ->with([0 => '1,234.56'])
                    ->getMock()
                )
            ->shouldReceive('setAttribute')
                ->once()
                ->with('action', 'actionUrl')
                ->andReturnSelf()
            ->getMock();

        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, $fee, true, true);
    }

    public function testUpdatePaymentSubmissonFormWithNoFeeAndIncomplete()
    {
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

        // assert fee amount is removed
        $formHelper->shouldReceive('remove')->once()->with($form, 'amount');

        // assert button is disabled
        $formHelper->shouldReceive('disableElement')->once()->with($form, 'submitPay');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, null, true, false);
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
