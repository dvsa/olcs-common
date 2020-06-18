<?php

/**
 * Variation Sole Trader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva\People\SoleTrader;

use Common\FormService\FormServiceInterface;
use Common\FormService\FormServiceManager;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\People\SoleTrader\VariationSoleTrader as Sut;
use Zend\Form\Form;

/**
 * Variation Sole Trader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSoleTraderTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $fsm;

    protected $sm;

    protected $mockVariationService;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->mockVariationService = m::mock(FormServiceInterface::class);

        $this->sm = Bootstrap::getServiceManager();

        /** @var FormServiceManager fsm */
        $this->fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $this->fsm->setServiceLocator($this->sm);
        $this->fsm->setService('lva-variation', $this->mockVariationService);

        $this->sut = new Sut();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    /**
     * @dataProvider noDisqualifyProvider
     */
    public function testGetFormNoDisqualify($params)
    {
        $params['canModify'] = true;

        $formActions = m::mock();
        $formActions->shouldReceive('has')->with('disqualify')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('disqualify');

        $form = m::mock();

        $this->mockVariationService->shouldReceive('alterForm')
            ->once()
            ->with($form);

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $this->sut->getForm($params);
    }

    public function testGetForm()
    {
        $params = [
            'location' => 'internal',
            'personId' => 123,
            'isDisqualified' => false,
            'canModify' => true,
            'disqualifyUrl' => 'foo'
        ];

        $formActions = m::mock();
        $formActions->shouldReceive('get->setValue')
            ->once()
            ->with('foo');

        $form = m::mock();

        $this->mockVariationService->shouldReceive('alterForm')
            ->once()
            ->with($form);

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $this->sut->getForm($params);
    }

    public function testGetFormCantModify()
    {
        $params = [
            'location' => 'internal',
            'personId' => 123,
            'isDisqualified' => false,
            'canModify' => false,
            'disqualifyUrl' => 'foo',
            'orgType' => 'bar'
        ];

        $formActions = m::mock();
        $formActions->shouldReceive('get->setValue')
            ->once()
            ->with('foo');

        $form = m::mock();

        $this->mockVariationService->shouldReceive('alterForm')
            ->once()
            ->with($form);

        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\SoleTrader')
            ->andReturn($form);

        $peopleService = m::mock();
        $peopleService->shouldReceive('lockPersonForm')
            ->once()
            ->with($form, 'bar');

        $this->sm->setService('Lva\People', $peopleService);

        $this->sut->getForm($params);
    }

    public function noDisqualifyProvider()
    {
        return [
            [
                ['location' => 'external']
            ],
            [
                [
                    'location' => 'internal',
                    'personId' => null
                ]
            ],
            [
                [
                    'location' => 'internal',
                    'personId' => 123,
                    'isDisqualified' => true
                ]
            ],
        ];
    }
}
