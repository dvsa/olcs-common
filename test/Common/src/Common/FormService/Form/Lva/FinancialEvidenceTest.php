<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\FinancialEvidence;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\FormService\Form\Lva\FinancialEvidence
 */
class FinancialEvidenceTest extends MockeryTestCase
{
    /** @var  FinancialEvidence */
    protected $sut;

    /** @var  m\MockInterface|\Common\Service\Helper\FormHelperService */
    protected $formHelper;
    /** @var  \Common\FormService\FormServiceManager */
    protected $fsm;

    public function setUp()
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->fsm = m::mock(\Common\FormService\FormServiceManager::class)->makePartial();

        $this->sut = new FinancialEvidence();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->fsm);
    }

    public function testGetForm()
    {
        /** @var \Zend\Http\Request $request */
        $request = m::mock(\Zend\Http\Request::class);

        // Mocks
        $mockForm = m::mock();

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialEvidence', $request)
            ->andReturn($mockForm);

        $form = $this->sut->getForm($request);

        $this->assertSame($mockForm, $form);
    }
}
