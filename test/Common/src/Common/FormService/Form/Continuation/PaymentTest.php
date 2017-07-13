<?php

namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Continuation\Payment;
use Common\Form\Model\Form\Continuation\Payment as PaymentForm;
use Common\Service\Helper\FormHelperService;
use CommonTest\Bootstrap;
use Common\FormService\FormServiceManager;

/**
 * Licence payment form service test
 */
class PaymentTest extends MockeryTestCase
{
    /** @var PaymentForm */
    protected $sut;
    /** @var  m\MockInterface */
    private $formHelper;

    protected $guidance;

    public function setUp()
    {
        $this->formHelper = m::mock(FormHelperService::class);
        $this->guidance = m::mock();

        $this->sut = new Payment();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Helper\Guidance', $this->guidance);

        $fsm = m::mock(FormServiceManager::class)->makePartial();
        $fsm->shouldReceive('getServiceLocator')->andReturn($sm);

        $this->sut->setFormServiceLocator($fsm);
        $this->sut->setFormHelper($this->formHelper);
    }

    public function testGetForm()
    {
        $form = m::mock(PaymentForm::class)
            ->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('remove')
                    ->with('pay')
                    ->once()
                    ->shouldReceive('get')
                    ->with('cancel')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('setLabel')
                        ->with('back-to-fees')
                        ->once()
                        ->shouldReceive('setAttribute')
                        ->andReturn('class', 'action--tertiary large')
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->twice()
            ->getMock();

        $this->formHelper
            ->shouldReceive('createForm')
            ->with(PaymentForm::class)
            ->andReturn($form)
            ->once()
            ->getMock();

        $this->guidance
            ->shouldReceive('append')
            ->with('selfserve-card-payments-disabled')
            ->once()
            ->getMock();

        $data = [
            'disableCardPayments' => true
        ];

        $this->assertEquals($form, $this->sut->getForm($data));
    }
}
