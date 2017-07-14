<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\FormServiceManager;
use Common\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Continuation\Declaration;
use Common\Form\Model\Form\Continuation\Declaration as FormModel;
use Common\Service\Helper\FormHelperService;

/**
 * Licence checklist form service test
 */
class DeclarationTest extends MockeryTestCase
{
    /** @var Declaration */
    protected $sut;
    /** @var  m\MockInterface */
    private $formHelper;

    public function setUp()
    {
        $this->formHelper = m::mock(FormHelperService::class);
        $this->sut = new Declaration();
        $this->sut->setFormHelper($this->formHelper);
    }

    public function testGetFormSignatureDisabled()
    {
        $urlService = m::mock();
        $translatorService = m::mock();
        $formHelperService = m::mock();
        $serviceLocator = m::mock();
        $serviceLocator->shouldReceive('get')->with('ControllerPluginManager')->andReturn(
            m::mock()->shouldReceive('get')->with('url')->andReturn($urlService)->getMock()
        );
        $serviceLocator->shouldReceive('get')->with('Helper\Translation')->andReturn($translatorService);
        $serviceLocator->shouldReceive('get')->with('Helper\Form')->andReturn($formHelperService);
        $formServiceLocator = m::mock(FormServiceManager::class);
        $formServiceLocator->shouldReceive('getServiceLocator')->with()->andReturn($serviceLocator);
        $this->sut->setFormServiceLocator($formServiceLocator);

        $contentElement = m::mock();
        $declarationElement = m::mock();
        $versionElement = m::mock();
        $form = m::mock();

        $this->formHelper->shouldReceive('createForm')->with(FormModel::class)->once()->andReturn($form);
        $form->shouldReceive('get')->with('content')->andReturn($contentElement);
        $contentElement->shouldReceive('get')->with('declaration')->andReturn($declarationElement);

        $urlService->shouldReceive('fromRoute')->with('continuation/declaration', [], [], true)->once()
            ->andReturn('URL');
        $translatorService->shouldReceive('translate')->with('print-declaration-form')->once()
            ->andReturn('print-declaration-form');
        $translatorService->shouldReceive('translateReplace')
            ->with('undertakings_declaration_download', ['URL', 'print-declaration-form'])->once()
            ->andReturn('undertakings_declaration_download');
        $contentElement->shouldReceive('get')->with('declarationDownload')->andReturn(
            m::mock()->shouldReceive('setAttribute')->with('value', 'undertakings_declaration_download')->getMock()
        );
        $declarationElement->shouldReceive('setValue')->with('DECLARATIONS')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->signatureOptions')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->declarationForVerify')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->sign')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->submitAndPay')->once();
        $form->shouldReceive('get')->with('version')->andReturn($versionElement);
        $versionElement->shouldReceive('setValue')->with(34)->once();

        $continuationDetailData = [
            'declarations' => 'DECLARATIONS',
            'disableSignatures' => true,
            'hasOutstandingContinuationFee' => false,
            'version' => 34,
        ];

        $this->assertEquals($form, $this->sut->getForm($continuationDetailData));
    }

    public function testGetFormSignatureEnabled()
    {
        $urlService = m::mock();
        $translatorService = m::mock();
        $formHelperService = m::mock();
        $serviceLocator = m::mock();
        $serviceLocator->shouldReceive('get')->with('ControllerPluginManager')->andReturn(
            m::mock()->shouldReceive('get')->with('url')->andReturn($urlService)->getMock()
        );
        $serviceLocator->shouldReceive('get')->with('Helper\Translation')->andReturn($translatorService);
        $serviceLocator->shouldReceive('get')->with('Helper\Form')->andReturn($formHelperService);
        $formServiceLocator = m::mock(FormServiceManager::class);
        $formServiceLocator->shouldReceive('getServiceLocator')->with()->andReturn($serviceLocator);
        $this->sut->setFormServiceLocator($formServiceLocator);

        $contentElement = m::mock();
        $declarationElement = m::mock();
        $versionElement = m::mock();
        $form = m::mock();

        $this->formHelper->shouldReceive('createForm')->with(FormModel::class)->once()->andReturn($form);
        $form->shouldReceive('get')->with('content')->andReturn($contentElement);
        $contentElement->shouldReceive('get')->with('declaration')->andReturn($declarationElement);

        $urlService->shouldReceive('fromRoute')->with('continuation/declaration', [], [], true)->once()
            ->andReturn('URL');
        $translatorService->shouldReceive('translate')->with('print-declaration-form')->once()
            ->andReturn('print-declaration-form');
        $translatorService->shouldReceive('translateReplace')
            ->with('undertakings_declaration_download', ['URL', 'print-declaration-form'])->once()
            ->andReturn('undertakings_declaration_download');
        $contentElement->shouldReceive('get')->with('declarationDownload')->andReturn(
            m::mock()->shouldReceive('setAttribute')->with('value', 'undertakings_declaration_download')->getMock()
        );
        $declarationElement->shouldReceive('setValue')->with('DECLARATIONS')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->disabledReview')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->submitAndPay')->once();
        $form->shouldReceive('get')->with('version')->andReturn($versionElement);
        $versionElement->shouldReceive('setValue')->with(34)->once();

        $continuationDetailData = [
            'declarations' => 'DECLARATIONS',
            'disableSignatures' => false,
            'hasOutstandingContinuationFee' => false,
            'version' => 34,
        ];

        $this->assertEquals($form, $this->sut->getForm($continuationDetailData));
    }

    public function testGetFormNoFees()
    {
        $urlService = m::mock();
        $translatorService = m::mock();
        $formHelperService = m::mock();
        $serviceLocator = m::mock();
        $serviceLocator->shouldReceive('get')->with('ControllerPluginManager')->andReturn(
            m::mock()->shouldReceive('get')->with('url')->andReturn($urlService)->getMock()
        );
        $serviceLocator->shouldReceive('get')->with('Helper\Translation')->andReturn($translatorService);
        $serviceLocator->shouldReceive('get')->with('Helper\Form')->andReturn($formHelperService);
        $formServiceLocator = m::mock(FormServiceManager::class);
        $formServiceLocator->shouldReceive('getServiceLocator')->with()->andReturn($serviceLocator);
        $this->sut->setFormServiceLocator($formServiceLocator);

        $contentElement = m::mock();
        $declarationElement = m::mock();
        $versionElement = m::mock();
        $form = m::mock();

        $this->formHelper->shouldReceive('createForm')->with(FormModel::class)->once()->andReturn($form);
        $form->shouldReceive('get')->with('content')->andReturn($contentElement);
        $contentElement->shouldReceive('get')->with('declaration')->andReturn($declarationElement);

        $urlService->shouldReceive('fromRoute')->with('continuation/declaration', [], [], true)->once()
            ->andReturn('URL');
        $translatorService->shouldReceive('translate')->with('print-declaration-form')->once()
            ->andReturn('print-declaration-form');
        $translatorService->shouldReceive('translateReplace')
            ->with('undertakings_declaration_download', ['URL', 'print-declaration-form'])->once()
            ->andReturn('undertakings_declaration_download');
        $contentElement->shouldReceive('get')->with('declarationDownload')->andReturn(
            m::mock()->shouldReceive('setAttribute')->with('value', 'undertakings_declaration_download')->getMock()
        );
        $declarationElement->shouldReceive('setValue')->with('DECLARATIONS')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->disabledReview')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->submitAndPay')->once();
        $form->shouldReceive('get')->with('version')->andReturn($versionElement);
        $versionElement->shouldReceive('setValue')->with(34)->once();

        $continuationDetailData = [
            'declarations' => 'DECLARATIONS',
            'disableSignatures' => false,
            'hasOutstandingContinuationFee' => false,
            'version' => 34,
        ];

        $this->assertEquals($form, $this->sut->getForm($continuationDetailData));
    }

    public function testGetFormWithFees()
    {
        $urlService = m::mock();
        $translatorService = m::mock();
        $formHelperService = m::mock();
        $serviceLocator = m::mock();
        $serviceLocator->shouldReceive('get')->with('ControllerPluginManager')->andReturn(
            m::mock()->shouldReceive('get')->with('url')->andReturn($urlService)->getMock()
        );
        $serviceLocator->shouldReceive('get')->with('Helper\Translation')->andReturn($translatorService);
        $serviceLocator->shouldReceive('get')->with('Helper\Form')->andReturn($formHelperService);
        $formServiceLocator = m::mock(FormServiceManager::class);
        $formServiceLocator->shouldReceive('getServiceLocator')->with()->andReturn($serviceLocator);
        $this->sut->setFormServiceLocator($formServiceLocator);

        $contentElement = m::mock();
        $declarationElement = m::mock();
        $versionElement = m::mock();
        $form = m::mock();

        $this->formHelper->shouldReceive('createForm')->with(FormModel::class)->once()->andReturn($form);
        $form->shouldReceive('get')->with('content')->andReturn($contentElement);
        $contentElement->shouldReceive('get')->with('declaration')->andReturn($declarationElement);

        $urlService->shouldReceive('fromRoute')->with('continuation/declaration', [], [], true)->once()
            ->andReturn('URL');
        $translatorService->shouldReceive('translate')->with('print-declaration-form')->once()
            ->andReturn('print-declaration-form');
        $translatorService->shouldReceive('translateReplace')
            ->with('undertakings_declaration_download', ['URL', 'print-declaration-form'])->once()
            ->andReturn('undertakings_declaration_download');
        $contentElement->shouldReceive('get')->with('declarationDownload')->andReturn(
            m::mock()->shouldReceive('setAttribute')->with('value', 'undertakings_declaration_download')->getMock()
        );
        $declarationElement->shouldReceive('setValue')->with('DECLARATIONS')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->disabledReview')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->submit')->once();
        $form->shouldReceive('get')->with('version')->andReturn($versionElement);
        $versionElement->shouldReceive('setValue')->with(34)->once();

        $continuationDetailData = [
            'declarations' => 'DECLARATIONS',
            'disableSignatures' => false,
            'hasOutstandingContinuationFee' => true,
            'version' => 34,
        ];

        $this->assertEquals($form, $this->sut->getForm($continuationDetailData));
    }

    public function testGetFormReviewSection()
    {
        $urlService = m::mock();
        $translatorService = m::mock();
        $formHelperService = m::mock();
        $serviceLocator = m::mock();
        $serviceLocator->shouldReceive('get')->with('ControllerPluginManager')->andReturn(
            m::mock()->shouldReceive('get')->with('url')->andReturn($urlService)->getMock()
        );
        $serviceLocator->shouldReceive('get')->with('Helper\Translation')->andReturn($translatorService);
        $serviceLocator->shouldReceive('get')->with('Helper\Form')->andReturn($formHelperService);
        $formServiceLocator = m::mock(FormServiceManager::class);
        $formServiceLocator->shouldReceive('getServiceLocator')->with()->andReturn($serviceLocator);
        $this->sut->setFormServiceLocator($formServiceLocator);

        $contentElement = m::mock();
        $declarationElement = m::mock();
        $versionElement = m::mock();
        $form = m::mock();

        $this->formHelper->shouldReceive('createForm')->with(FormModel::class)->once()->andReturn($form);
        $form->shouldReceive('get')->with('content')->andReturn($contentElement);
        $contentElement->shouldReceive('get')->with('declaration')->andReturn($declarationElement);

        $urlService->shouldReceive('fromRoute')->with('continuation/declaration', [], [], true)->once()
            ->andReturn('URL');
        $translatorService->shouldReceive('translate')->with('print-declaration-form')->once()
            ->andReturn('print-declaration-form');
        $translatorService->shouldReceive('translateReplace')
            ->with('undertakings_declaration_download', ['URL', 'print-declaration-form'])->once()
            ->andReturn('undertakings_declaration_download');
        $contentElement->shouldReceive('get')->with('declarationDownload')->andReturn(
            m::mock()->shouldReceive('setAttribute')->with('value', 'undertakings_declaration_download')->getMock()
        );
        $declarationElement->shouldReceive('setValue')->with('DECLARATIONS')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'content->disabledReview')->once();
        $formHelperService->shouldReceive('remove')->with($form, 'form-actions->submit')->once();
        $form->shouldReceive('get')->with('version')->andReturn($versionElement);
        $versionElement->shouldReceive('setValue')->with(34)->once();

        $contentElement->shouldReceive('get')->with('review')->andReturn(
            m::mock()->shouldReceive('setTokens')->with(['application.review-declarations.review.business-owner'])
                ->once()->getMock()
        );

        $continuationDetailData = [
            'declarations' => 'DECLARATIONS',
            'disableSignatures' => false,
            'hasOutstandingContinuationFee' => true,
            'version' => 34,
            'organisationTypeId' => RefData::ORG_TYPE_SOLE_TRADER,
        ];

        $this->assertEquals($form, $this->sut->getForm($continuationDetailData));
    }
}
