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
use Common\Service\Entity\LicenceEntityService;

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
        $actionUrl = 'actionUrl';
        $feeAmount = '1358.01';

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
            'Helper\Translation',
            m::mock()
                ->shouldReceive('translateReplace')
                    ->once()
                    ->with('application.payment-submission.amount.value', array('1,358.01'))
                    ->andReturn('HTML VALUE')
                ->getMock()
        );

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, true, true, $feeAmount);
    }

    /**
     * @dataProvider noFeeProvider
     */
    public function testUpdatePaymentSubmissonFormWithNoFeesAndIncomplete($feeAmount)
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

        $this->sut->updatePaymentSubmissonForm($form, $actionUrl, true, false, $feeAmount);
    }

    public function noFeeProvider()
    {
        return [
            ['0'],
            [0],
            ['0.00'],
            [0.00],
        ];
    }
    public function testUpdatePaymentSubmissonFormAlreadySubmitted()
    {
        $form = m::mock('\Zend\Form\Form');
        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        // assert button and fee amount are removed
        $formHelper->shouldReceive('remove')->once()->with($form, 'submitPay');
        $formHelper->shouldReceive('remove')->once()->with($form, 'amount');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, '', false, false, 'foo');
    }

    public function testUpdatePaymentSubmissonFormDisableCardPayments()
    {
        $form = m::mock('\Zend\Form\Form');
        $formHelper = m::mock('Common\Service\Helper\FormHelperService');

        $form = m::mock('\Zend\Form\Form')
            ->shouldReceive('setAttribute')
                ->once()
                ->with('action', '')
                ->andReturnSelf()
            ->shouldReceive('get')
                ->with('submitPay')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('setLabel')
                        ->twice()
                        ->with('submit-application.button')
                    ->getMock()
                )
            ->getMock();

        $formHelper->shouldReceive('remove')->once()->with($form, 'amount');

        $this->sm->setService('Helper\Form', $formHelper);

        $this->sut->updatePaymentSubmissonForm($form, '', true, true, 'foo', true);
    }
}
