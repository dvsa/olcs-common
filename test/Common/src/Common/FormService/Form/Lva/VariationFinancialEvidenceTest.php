<?php

namespace CommonTest\Common\FormService\Form\Lva;

use Common\Form\Form;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Laminas\Form\ElementInterface;
use Laminas\Form\Fieldset;
use Laminas\ServiceManager\ServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\VariationFinancialEvidence;

/**
 * Variation Financial Evidence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialEvidenceTest extends MockeryTestCase
{
    /** @var  VariationFinancialEvidence */
    protected $sut;
    /** @var  m\MockInterface|\Common\Service\Helper\FormHelperService */
    protected $formHelper;
    /** @var  \Common\FormService\FormServiceManager */
    protected $fsm;
    /** @var  m\MockInterface */
    protected $urlHelper;
    /** @var  m\MockInterface */
    protected $translator;

    public function setUp(): void
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->fsm = m::mock(\Common\FormService\FormServiceManager::class)->makePartial();
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->translator = m::mock(TranslationHelperService::class);
        $this->authService = m::mock(\LmcRbacMvc\Service\AuthorizationService::class);

        $sm = new ServiceManager();

        $sm->setService('Helper\Url', $this->urlHelper);
        $sm->setService('Helper\Translation', $this->translator);

        $this->fsm->shouldReceive('getServiceLocator')->andReturn($sm);
        $this->sut = new VariationFinancialEvidence($this->formHelper, $this->authService, $this->translator, $this->urlHelper);
    }

    public function testGetForm()
    {
        $this->translator
            ->shouldReceive('translateReplace')
            ->with('lva-financial-evidence-evidence.hint', ['FOO'])
            ->andReturn('BAR')
            ->once()
            ->getMock();

        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with('guides/guide', ['guide' => 'financial-evidence'], [], true)
            ->andReturn('FOO')
            ->once()
            ->getMock();

        /** @var \Laminas\Http\Request $request */
        $request = m::mock(\Laminas\Http\Request::class);

        // Mocks

        $formActions = m::mock(ElementInterface::class);
        $formActions->shouldReceive('has')->with('saveAndContinue')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('saveAndContinue');

        $mockForm = m::mock(Form::class)
            ->shouldReceive('get')
            ->with('evidence')
            ->andReturn(
                m::mock(Fieldset::class)
                    ->shouldReceive('get')
                    ->with('uploadNowRadio')
                    ->andReturn(
                        m::mock(ElementInterface::class)
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('uploadLaterRadio')
                    ->andReturn(
                        m::mock(ElementInterface::class)
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('get')
                    ->with('sendByPostRadio')
                    ->andReturn(
                        m::mock(ElementInterface::class)
                            ->shouldReceive('setName')
                            ->with('uploadNow')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('setOption')
                    ->with('hint', 'BAR')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('has')
            ->with('form-actions')
            ->andReturn(true)
            ->shouldReceive('get')
            ->with('form-actions')
            ->andReturn($formActions)
            ->getMock();

        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\FinancialEvidence', $request)
            ->andReturn($mockForm)
            ->shouldReceive('remove')
            ->with($mockForm, 'evidence->uploadNow')
            ->once()
            ->getMock();

        $form = $this->sut->getForm($request);

        $this->assertSame($mockForm, $form);
    }
}
