<?php

namespace Common\Controller\Lva;

use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVariationController extends AbstractController
{
    use Traits\CreateVariationTrait;

    protected TranslationHelperService $translationHelper;
    protected $processingCreateVariation;

    /**
     * @param NiTextTranslation $niTextTranslationUtil
     * @param AuthorizationService $authService
     * @param TranslationHelperService $translationHelper
     * @param $processingCreateVariation
     */
    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        TranslationHelperService $translationHelper,
        $processingCreateVariation
    ) {
        $this->processingCreateVariation = $processingCreateVariation;
        $this->translationHelper = $translationHelper;
        parent::__construct($niTextTranslationUtil, $authService);
    }

    /**
     * Index action
     *
     * @return \Common\View\Model\Section
     */
    public function indexAction()
    {
        $form = $this->processForm();

        if (! ($form instanceof Form)) {
            return $form;
        }

        return $this->render(
            'create-variation-confirmation',
            $form,
            ['sectionText' => $this->translationHelper->translate('markup-licence-changes-confirmation-text')]
        );
    }
}
