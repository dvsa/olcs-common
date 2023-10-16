<?php

namespace Common\Controller\Lva;

use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\DataHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use ZfcRbac\Service\AuthorizationService;

/**
 * Vehicles Declarations Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractVehiclesDeclarationsController extends AbstractController
{
    /**
     * Action data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'application',
                'smallVehiclesIntention',
                'nineOrMore',
                'mainOccupation',
                'limousinesNoveltyVehicles'
            )
        )
    );

    protected $data;

    protected DataHelperService $dataHelper;
    protected ScriptFactory $scriptFactory;
    protected FormServiceManager $formServiceManager;
    protected FormHelperService $formHelper;

    /**
     * @param NiTextTranslation $niTextTranslationUtil
     * @param AuthorizationService $authService
     * @param FormHelperService $formHelper
     * @param FormServiceManager $formServiceManager
     * @param ScriptFactory $scriptFactory
     * @param DataHelperService $dataHelper
     */
    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        FormHelperService $formHelper,
        FormServiceManager $formServiceManager,
        ScriptFactory $scriptFactory,
        DataHelperService $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->scriptFactory = $scriptFactory;
        $this->formServiceManager = $formServiceManager;
        $this->formHelper = $formHelper;
        parent::__construct($niTextTranslationUtil, $authService);
    }

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getForm()->setData($data);

        $this->alterForm($form, $data);

        $this->scriptFactory->loadFile('vehicle-declarations');

        if ($request->isPost() && $form->isValid()) {
            $this->save($data);

            return $this->completeSection('vehicles_declarations');
        }

        return $this->render('vehicles_declarations', $form);
    }

    protected function getForm()
    {
        return $this->formServiceManager
            ->get('lva-' . $this->lva . '-vehicles_declarations')
            ->getForm();
    }

    protected function getFormData()
    {
        return $this->formatDataForForm($this->loadData());
    }

    protected function loadData()
    {
        if ($this->data === null) {
            $response = $this->handleQuery(
                \Dvsa\Olcs\Transfer\Query\Application\VehicleDeclaration::create(['id' => $this->getApplicationId()])
            );
            if (!$response->isOk()) {
                throw new \RuntimeException('Error getting vehicle declaration');
            }
            $this->data = $response->getResult();
        }

        return $this->data;
    }

    /**
     * Format data for dorm
     *
     * @param arary $data
     * @return array
     */
    protected function formatDataForForm($data)
    {
        $psvVehicleSize = $data['psvWhichVehicleSizes']['id'] ?? null;
        return array(
            'version' => $data['version'],
            'psvVehicleSize' => array(
                'size' => $psvVehicleSize,
            ),
            'smallVehiclesIntention' => array(
                'psvOperateSmallVhl' => $data['psvOperateSmallVhl'],
                'psvSmallVhlNotes' => $data['psvSmallVhlNotes'],
                'psvSmallVhlConfirmation' => $data['psvSmallVhlConfirmation']
            ),
            'nineOrMore' => array(
                'psvNoSmallVhlConfirmation' => $data['psvNoSmallVhlConfirmation']
            ),
            'mainOccupation' => array(
                'psvMediumVhlConfirmation' => $data['psvMediumVhlConfirmation'],
                'psvMediumVhlNotes' => $data['psvMediumVhlNotes']
            ),
            'limousinesNoveltyVehicles' => array(
                'psvLimousines' => $data['psvLimousines'],
                'psvNoLimousineConfirmation' => $data['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $data['psvOnlyLimousinesConfirmation']
            )
        );
    }

    /**
     * Add customisation to the form dependent on which of five scenarios
     * is in play for OLCS-2855
     *
     * @param Laminas\Form\Form $form
     * @return Laminas\Form\Form
     */
    protected function alterForm(\Laminas\Form\Form $form, $formData)
    {
        $this->alterFormForLva($form);

        // We always need to load data, even if we have posted, so we know how to alter the form
        $data = $this->loadData();

        $formHelper = $this->formHelper;

        $isScotland = isset($data['licence']['trafficArea']['isScotland']) &&
            $data['licence']['trafficArea']['isScotland'];

        // if Vehicle size not selected then
        if (!isset($formData['psvVehicleSize']['size'])) {
            // only validate the vehicle size
            $validationGroup = ['psvVehicleSize'];
        } else {
            // start with validating everything
            $validationGroup = [
                'psvVehicleSize',
                // 15bi, 15bii, 15c/d
                'smallVehiclesIntention' => ['psvOperateSmallVhl', 'psvSmallVhlNotes', 'psvSmallVhlConfirmation'],
                // 15e
                'nineOrMore' => ['psvNoSmallVhlConfirmation'],
                // section 10, 8
                'mainOccupation' => ['psvMediumVhlConfirmation', 'psvMediumVhlNotes'],
                // 15fi, 15fii, 15g
                'limousinesNoveltyVehicles' => [
                    'psvLimousines',
                    'psvNoLimousineConfirmation',
                    'psvOnlyLimousinesConfirmation',
                ],
            ];

            if ($formData['psvVehicleSize']['size'] === \Common\RefData::PSV_VEHICLE_SIZE_SMALL) {
                unset($validationGroup['mainOccupation']);
                unset($validationGroup['limousinesNoveltyVehicles'][2]);
            }

            if ($formData['psvVehicleSize']['size'] === \Common\RefData::PSV_VEHICLE_SIZE_MEDIUM_LARGE) {
                unset($validationGroup['smallVehiclesIntention']);
            }

            if ($formData['psvVehicleSize']['size'] !== \Common\RefData::PSV_VEHICLE_SIZE_MEDIUM_LARGE) {
                unset($validationGroup['nineOrMore']);
            }
        }

        // if Scotland remove 15bi and 15bii
        if ($isScotland) {
            $formHelper->remove($form, 'smallVehiclesIntention->psvOperateSmallVhl');
            $formHelper->remove($form, 'smallVehiclesIntention->psvSmallVhlNotes');
            if (isset($validationGroup['smallVehiclesIntention'][0])) {
                unset($validationGroup['smallVehiclesIntention'][0]);
            }
            if (isset($validationGroup['smallVehiclesIntention'][1])) {
                unset($validationGroup['smallVehiclesIntention'][1]);
            }
        }

        // Section 10 only visible for Restricted licences
        if ($data['licenceType']['id'] !== RefData::LICENCE_TYPE_RESTRICTED) {
            $formHelper->remove($form, 'mainOccupation');
            if (isset($validationGroup['mainOccupation'])) {
                unset($validationGroup['mainOccupation']);
            }
        }

        $form->setValidationGroup($validationGroup);
    }

    /**
     * Save the form data
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data)
    {
        $saveData = $this->dataHelper->processDataMap($data, $this->dataMap);
        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();
        $saveData['psvVehicleSize'] = $data['psvVehicleSize']['size'];

        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleDeclaration::create($saveData)
        );
        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating vehicle declaration');
        }
    }
}
