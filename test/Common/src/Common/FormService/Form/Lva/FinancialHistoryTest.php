<?php

/**
 * Financial History Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\FinancialHistory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Financial History Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialHistoryTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $fsm;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new FinancialHistory();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    public function testGetForm()
    {
        $request = m::mock();

        // Mocks
        $mockForm = m::mock();

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialHistory', $request)
            ->andReturn($mockForm);

        $form = $this->sut->getForm($request);

        $this->assertSame($mockForm, $form);
    }
}
