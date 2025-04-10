<?php

namespace Common\Controller\Lva;

use Common\Data\Mapper\Lva\Evidence;
use Common\Data\Mapper\Lva\PsvMainOccupationUndertakings;
use Common\Data\Mapper\Lva\PsvOperateLarge;
use Common\Data\Mapper\Lva\PsvOperateNovelty;
use Common\Data\Mapper\Lva\PsvOperateSmall;
use Common\Data\Mapper\Lva\PsvSmallConditions;
use Common\Data\Mapper\Lva\PsvWrittenExplanation;
use Common\Data\Mapper\Lva\VehicleSize;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\DataHelperService;
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
    private array $mapperClasses = [
        'vehicles_size' => VehicleSize::class,
        'psv_operate_large' => PsvOperateLarge::class,
        'psv_operate_small' => PsvOperateSmall::class,
        'psv_small_part_written' => PsvWrittenExplanation::class,
        'psv_small_conditions' => PsvSmallConditions::class,
        'psv_operate_novelty' => PsvOperateNovelty::class,
        'psv_documentary_evidence_small' => Evidence::class,
        'psv_documentary_evidence_large' => Evidence::class,
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
        protected DataHelperService $dataHelper
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

    public function smallEvidenceAction()
    {
        $this->scriptFactory->loadFile('financial-evidence');
        return $this->handleSection('psv_documentary_evidence_small');
    }

    public function largeEvidenceAction()
    {
        $this->scriptFactory->loadFile('financial-evidence');
        return $this->handleSection('psv_documentary_evidence_large');
    }

    public function mainOccupationAction()
    {
        return $this->handleSection('psv_main_occupation_undertakings');
    }

    public function writtenExplanationAction()
    {
        return $this->handleSection('psv_small_part_written');
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
}
