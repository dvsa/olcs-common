<?php

namespace Common\Controller\Lva\Licence;

use Common\Controller\Lva;
use Common\Data\Mapper\Lva\TypeOfLicence as TypeOfLicenceMapper;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Lva\VariationLvaService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Transfer\Query\Licence\TypeOfLicence;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Laminas\Http\Response;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends Lva\AbstractTypeOfLicenceController
{
    protected FlashMessengerHelperService $flashMessengerHelper;

    protected ScriptFactory $scriptFactory;

    protected FormServiceManager $formServiceManager;

    protected VariationLvaService $variationLvaService;

    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        FlashMessengerHelperService $flashMessengerHelper,
        ScriptFactory $scriptFactory,
        FormServiceManager $formServiceManager,
        VariationLvaService $variationLvaService
    ) {
        $this->scriptFactory = $scriptFactory;
        $this->flashMessengerHelper = $flashMessengerHelper;
        $this->formServiceManager = $formServiceManager;
        $this->variationLvaService = $variationLvaService;

        parent::__construct($niTextTranslationUtil, $authService, $flashMessengerHelper, $scriptFactory);
    }

    /**
     * Licence type of licence section
     *
     * @return \Common\View\Model\Section|Response
     */
    public function indexAction()
    {
        $response = $this->handleQuery(TypeOfLicence::create(['id' => $this->getIdentifier()]));

        if ($response->isClientError() || $response->isServerError()) {
            $this->flashMessengerHelper->addErrorMessage('unknown-error');
            return $this->notFoundAction();
        }

        $data = $response->getResult();

        $this->variationLvaService->addVariationMessage(
            $this->getIdentifier(),
            'type_of_licence'
        );

        $params = [
            'canBecomeSpecialRestricted' => $data['canBecomeSpecialRestricted'],
            'canUpdateLicenceType' => $data['canUpdateLicenceType']
        ];

        /** @var \Laminas\Form\FormInterface $form */
        $form = $this->formServiceManager
            ->get('lva-licence-type-of-licence')
            ->getForm($params);

        $form->setData(TypeOfLicenceMapper::mapFromResult($data));

        return $this->renderIndex($form);
    }
}
