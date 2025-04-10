<?php

namespace Common\Controller\Lva;

use Common\Controller\Traits\GenericUpload;
use Common\Data\Mapper\Lva\PsvSmallEvidence;
use Common\Data\Mapper\Lva\PsvLargeEvidence;
use Common\Data\Mapper\Lva\PsvMainOccupationUndertakings;
use Common\Data\Mapper\Lva\PsvOperateLarge;
use Common\Data\Mapper\Lva\PsvOperateNovelty;
use Common\Data\Mapper\Lva\PsvOperateSmall;
use Common\Data\Mapper\Lva\PsvSmallConditions;
use Common\Data\Mapper\Lva\PsvWrittenExplanation;
use Common\Data\Mapper\Lva\VehicleSize;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\DataHelperService;
use Common\Service\Helper\FileUploadHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Transfer\Command\Application\UpdateMainOccupationEvidence;
use Dvsa\Olcs\Transfer\Command\Application\UpdateMainOccupationUndertakings;
use Dvsa\Olcs\Transfer\Command\Application\UpdateNoveltyVehicles;
use Dvsa\Olcs\Transfer\Command\Application\UpdateSmallVehicleConditionsAndUndertaking;
use Dvsa\Olcs\Transfer\Command\Application\UpdateSmallVehicleEvidence;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleNinePassengers;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleOperatingSmall;
use Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleSize;
use Dvsa\Olcs\Transfer\Command\Application\UpdateWrittenExplanation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use LmcRbacMvc\Service\AuthorizationService;

abstract class AbstractVehiclesDeclarationsController extends AbstractController
{
    use GenericUpload;

    protected $documents = null;

    private array $mapperClasses = [
        'vehicles_size' => VehicleSize::class,
        'psv_operate_large' => PsvOperateLarge::class,
        'psv_operate_small' => PsvOperateSmall::class,
        'psv_small_part_written' => PsvWrittenExplanation::class,
        'psv_small_conditions' => PsvSmallConditions::class,
        'psv_operate_novelty' => PsvOperateNovelty::class,
        'psv_documentary_evidence_small' => PsvSmallEvidence::class,
        'psv_documentary_evidence_large' => PsvLargeEvidence::class,
        'psv_main_occupation_undertakings' => PsvMainOccupationUndertakings::class,
    ];

    private array $updateCommands = [
        'vehicles_size' => UpdateVehicleSize::class,
        'psv_operate_large' => UpdateVehicleNinePassengers::class,
        'psv_operate_small' => UpdateVehicleOperatingSmall::class,
        'psv_small_part_written' => UpdateWrittenExplanation::class,
        'psv_small_conditions' => UpdateSmallVehicleConditionsAndUndertaking::class,
        'psv_operate_novelty' => UpdateNoveltyVehicles::class,
        'psv_documentary_evidence_small' => UpdateSmallVehicleEvidence::class,
        'psv_documentary_evidence_large' => UpdateMainOccupationEvidence::class,
        'psv_main_occupation_undertakings' => UpdateMainOccupationUndertakings::class,
    ];

    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        protected FormHelperService $formHelper,
        protected FormServiceManager $formServiceManager,
        protected ScriptFactory $scriptFactory,
        protected DataHelperService $dataHelper,
        protected FileUploadHelperService $uploadHelper,
    ) {
        parent::__construct($niTextTranslationUtil, $authService);
    }

    public function handleSection(string $section)
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost()->getArrayCopy() : $this->fetchFormData($section);

        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_' . $section)->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveSection($section, $data);

            return $this->completeSection($section);
        }

        return $this->render($section, $form);
    }

    private function saveSection(string $section, array $data)
    {
        $mapperClass = $this->getMapperForSection($section);
        $saveData = $mapperClass::mapFromForm($data);
        $saveData['id'] = $this->getApplicationId();

        $updateClass = $this->getUpdateClassForSection($section);

        /** @var CommandInterface $updateCmd */
        $updateCmd = $updateClass::create($saveData);
        $response = $this->handleCommand($updateCmd);

        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating section: ' . $section);
        }
    }

    private function fetchFormData(string $section): array
    {
        $mapperClass = $this->getMapperForSection($section);

        // Load data and map it to the form
        $data = $this->loadData();
        return $mapperClass::mapFromResult($data);
    }

    private function getMapperForSection(string $section): string
    {
        $mapperClass = $this->mapperClasses[$section] ?? null;

        if ($mapperClass === null) {
            throw new \RuntimeException('No mapper class found for section: ' . $section);
        }

        return $mapperClass;
    }

    private function getUpdateClassForSection(string $section): string
    {
        $updateClass = $this->updateCommands[$section] ?? null;

        if ($updateClass === null) {
            throw new \RuntimeException('No transfer object found to update section: ' . $section);
        }

        return $updateClass;
    }

    public function sizeAction()
    {
        return $this->handleSection('vehicles_size');
    }

    public function operateLargeAction()
    {
        return $this->handleSection('psv_operate_large');
    }

    public function noveltyAction()
    {
        $this->scriptFactory->loadFile('vehicle-limo');
        return $this->handleSection('psv_operate_novelty');
    }

    public function operateSmallAction()
    {
        return $this->handleSection('psv_operate_small');
    }

    public function smallConditionsAction()
    {
        return $this->handleSection('psv_small_conditions');
    }

    public function mainOccupationAction()
    {
        return $this->handleSection('psv_main_occupation_undertakings');
    }

    public function writtenExplanationAction()
    {
        return $this->handleSection('psv_small_part_written');
    }

    public function smallEvidenceAction()
    {
        return $this->handleEvidenceSection('psv_documentary_evidence_small');
    }

    public function largeEvidenceAction()
    {
        return $this->handleEvidenceSection('psv_documentary_evidence_large');
    }

    public function handleEvidenceSection(string $section)
    {
        $this->scriptFactory->loadFile('financial-evidence');
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost()->getArrayCopy() : $this->fetchFormData($section);

        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_declarations_' . $section)->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->saveSection($section, $data);

            return $this->completeSection($section);
        }

        // handle files
//        if ($form->has('MultipleFileUpload')) {
//            $hasProcessedFiles = $this->processFiles(
//                $form,
//                'MultipleFileUpload',
//                $this->processFileUpload(...),
//                $this->deleteFile(...),
//                $this->getDocuments(...)
//            );
//
//            if (!empty($form->getMessages())) {
//                $form->preventSuccessfulValidation();
//            }
//        }
//
//        // update application record and redirect
//        if (!$hasProcessedFiles && $request->isPost() && $form->isValid() && $this->saveEvidenceSection($formData, $section)) {
//            return $this->completeSection($section);
//        }

        // load scripts
        $this->scriptFactory->loadFile('financial-evidence');

        return $this->render($section, $form);
    }

//    public function processFileUpload(array $file): void
//    {
//        $this->documents = null;
//
//        $data = $this->loadData();
//        $response = $this->handleQuery($query);
//        $result = $response->getResult();
//
//        $data = [
//            'description' => $file['name'],
//            'category' => 1,
//            'subCategory' => 1,
//            'isExternal'  => true,
//            'application' => $this->getApplicationId(),
//        ];
//
//        $this->uploadFile($file, $data);
//    }

    /**
     * Get documents relating to the application
     *
     * @return array
     */
//    public function getDocuments()
//    {
//        if ($this->documents === null) {
//            $params = [
//                'id' => 1,
//                'category' => 1,
//                'subCategory' => 1,
//            ];
//
//            //$response = $this->handleQuery(Documents::create($params));
//            //$this->documents = $response->getResult();
//        }
//
//        return $this->documents;
//    }

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
}
