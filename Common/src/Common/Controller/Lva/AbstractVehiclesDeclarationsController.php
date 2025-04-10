<?php

namespace Common\Controller\Lva;

use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\DataHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use LmcRbacMvc\Service\AuthorizationService;

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
    protected $dataMap = [
        'main' => [
            'mapFrom' => [
                'application',
                'smallVehiclesIntention',
                'nineOrMore',
                'mainOccupation',
                'limousinesNoveltyVehicles'
            ]
        ]
    ];

    protected $data;

    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        protected FormHelperService $formHelper,
        protected FormServiceManager $formServiceManager,
        protected ScriptFactory $scriptFactory,
        protected DataHelperService $dataHelper
    ) {
        parent::__construct($niTextTranslationUtil, $authService);
    }

    public function sizeAction()
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost() : $this->getSizeFormData();

        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_size')->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveDataForSize($data);

            return $this->completeSection('vehicles_size');
        }

        return $this->render('vehicles_size', $form);
    }

    protected function getSizeFormData()
    {
        return $this->formatDataForSizeForm($this->loadData());
    }

    protected function formatDataForSizeForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'psvVehicleSize' => [
                'size' => $data['psvWhichVehicleSizes']['id'] ?? null,
            ],
        ];
    }

    protected function saveDataForSize($data): void
    {
        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();
        $saveData['psvVehicleSize'] = $data['psvVehicleSize']['size'];

        $command = \Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleSize::create($saveData);
        $response = $this->handleCommand($command);

        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating vehicle size');
        }
    }

    public function sizeNineAction()
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost() : $this->getSizeNineFormData();

        $this->scriptFactory->loadFile('vehicle-limo');
        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_large')->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveDataForSize($data);

            return $this->completeSection('vehicles_size_nine');
        }

        return $this->render('vehicles_size_nine', $form);
    }

    protected function getSizeNineFormData()
    {
        return $this->formatDataForSizeForm($this->loadData());
    }

    protected function formatDataForSizeNineForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'psvVehicleSize' => [
                'size' => $data['psvWhichVehicleSizes']['id'] ?? null,
            ],
        ];
    }

    protected function saveDataForSizeNine($data): void
    {
        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();
        $saveData['psvVehicleSize'] = $data['psvVehicleSize']['size'];

        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleSize::create($saveData)
        );

        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating vehicle size nine seats or less');
        }
    }

    public function operatingSmallAction()
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost() : $this->getSizeFormData();

        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_both')->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveDataForSize($data);

            return $this->completeSection('operating_small_vehicles');
        }

        return $this->render('operating_small_vehicles', $form);
    }

    protected function getOperatingSmallFormData()
    {
        return $this->formatDataForSizeForm($this->loadData());
    }

    protected function formatDataForOperatingSmallForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'smallVehiclesIntention' => [
                'psvOperateSmallVhl' => $data['psvOperateSmallVhl'],
                'psvSmallVhlConfirmation' => $data['psvSmallVhlConfirmation']
            ],
        ];
    }

    public function smallConditionsAction()
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost() : $this->getSmallConditionsFormData();

        $this->scriptFactory->loadFile('vehicle-limo');
        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_small')->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveDataForSmallConditions($data);

            return $this->completeSection('small_vehicles_condition_undertakings');
        }

        return $this->render('small_vehicles_condition_undertakings', $form);
    }

    protected function getSmallConditionsFormData()
    {
        return $this->formatDataForSmallConditionsForm($this->loadData());
    }

    protected function formatDataForSmallConditionsForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'smallVehiclesIntention' => [
                'psvSmallVhlConfirmation' => $data['psvSmallVhlConfirmation']
            ],
            'limousinesNoveltyVehicles' => [
                'psvLimousines' => $data['psvLimousines'],
                'psvNoLimousineConfirmation' => $data['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $data['psvOnlyLimousinesConfirmation']
            ]
        ];
    }

    protected function saveDataForSmallConditions($data): void
    {
        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();
        $saveData['psvVehicleSize'] = $data['psvVehicleSize']['size'];

        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleSize::create($saveData)
        );

        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating small vehicle condition and undertakings');
        }
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
     * @param array $data
     * @return array
     */
    protected function formatDataForForm($data)
    {
        $psvVehicleSize = $data['psvWhichVehicleSizes']['id'] ?? null;
        return [
            'version' => $data['version'],
            'psvVehicleSize' => [
                'size' => $psvVehicleSize,
            ],
            'smallVehiclesIntention' => [
                'psvOperateSmallVhl' => $data['psvOperateSmallVhl'],
                'psvSmallVhlNotes' => $data['psvSmallVhlNotes'],
                'psvSmallVhlConfirmation' => $data['psvSmallVhlConfirmation']
            ],
            'nineOrMore' => [
                'psvNoSmallVhlConfirmation' => $data['psvNoSmallVhlConfirmation']
            ],
            'mainOccupation' => [
                'psvMediumVhlConfirmation' => $data['psvMediumVhlConfirmation'],
                'psvMediumVhlNotes' => $data['psvMediumVhlNotes']
            ],
            'limousinesNoveltyVehicles' => [
                'psvLimousines' => $data['psvLimousines'],
                'psvNoLimousineConfirmation' => $data['psvNoLimousineConfirmation'],
                'psvOnlyLimousinesConfirmation' => $data['psvOnlyLimousinesConfirmation']
            ]
        ];
    }

    /**
     * Add customisation to the form dependent on which of five scenarios
     * is in play for OLCS-2855
     */
    protected function alterForm(\Laminas\Form\Form $form, $formData): void
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
     *
     * @return void
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
