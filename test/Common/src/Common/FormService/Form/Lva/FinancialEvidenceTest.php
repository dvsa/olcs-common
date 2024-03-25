<?php

declare(strict_types=1);

namespace CommonTest\Common\FormService\Form\Lva;

use Common\FormService\Form\Lva\FinancialEvidence;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Common\Validator\ValidateIf;
use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\FormInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\InputFilter\InputInterface;
use Laminas\Validator\ValidatorPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use LmcRbacMvc\Service\AuthorizationService;

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
    /** @var  m\MockInterface */
    protected $urlHelper;
    /** @var  m\MockInterface */
    protected $translator;

    public function setUp(): void
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->fsm = m::mock(\Common\FormService\FormServiceManager::class)->makePartial();
        $this->authService = m::mock(AuthorizationService::class);
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->translator = m::mock(TranslationHelperService::class);
        $this->validatorPluginManager = m::mock(ValidatorPluginManager::class);

        $this->sut = new FinancialEvidence(
            $this->formHelper,
            $this->authService,
            $this->translator,
            $this->urlHelper,
            $this->validatorPluginManager
        );
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

        $uploadNowRadioElement = m::mock(ElementInterface::class);
        $uploadNowRadioElement->expects('setName')->with('uploadNow');

        $uploadLaterRadioElement = m::mock(ElementInterface::class);
        $uploadLaterRadioElement->expects('setName')->with('uploadNow');

        $sendByPostRadioElement = m::mock(ElementInterface::class);
        $sendByPostRadioElement->expects('setName')->with('uploadNow');

        $evidenceFieldset = m::mock(FieldsetInterface::class);
        $evidenceFieldset->expects('get')->with('uploadNowRadio')->andReturn($uploadNowRadioElement);
        $evidenceFieldset->expects('get')->with('uploadLaterRadio')->andReturn($uploadLaterRadioElement);
        $evidenceFieldset->expects('get')->with('sendByPostRadio')->andReturn($sendByPostRadioElement);
        $evidenceFieldset->expects('setOption')->with('hint', 'BAR');

        $validateIfValidator = m::mock(ValidateIf::class);
        $validateIfValidator->expects('setOptions')->with(m::type('array'));

        $this->validatorPluginManager->expects('get')->with(ValidateIf::class)->andReturn($validateIfValidator);

        $fileCountInput = m::mock(InputInterface::class);
        $fileCountInput->expects('getValidatorChain->attach')->with($validateIfValidator);

        $uploadNowInput = m::mock(InputInterface::class);
        $uploadNowInput->expects('setRequired')->with(false);

        $uploadLaterInput = m::mock(InputInterface::class);
        $uploadLaterInput->expects('setRequired')->with(false);

        $sendByPostInput = m::mock(InputInterface::class);
        $sendByPostInput->expects('setRequired')->with(false);

        $evidenceInputFilter = m::mock(InputFilterInterface::class);
        $evidenceInputFilter->expects('get')->with('uploadedFileCount')->andReturn($fileCountInput);
        $evidenceInputFilter->expects('get')->with('uploadNowRadio')->andReturn($uploadNowInput);
        $evidenceInputFilter->expects('get')->with('uploadLaterRadio')->andReturn($uploadLaterInput);
        $evidenceInputFilter->expects('get')->with('sendByPostRadio')->andReturn($sendByPostInput);

        $inputFilterInterface = m::mock(InputFilterInterface::class);
        $inputFilterInterface->expects('get')->with('evidence')->andReturn($evidenceInputFilter);

        // Mocks
        $mockForm = m::mock(FormInterface::class);
        $mockForm->expects('getInputFilter')->withNoArgs()->andReturn($inputFilterInterface);
        $mockForm->expects('get')
            ->with('evidence')
            ->andReturn($evidenceFieldset);

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
