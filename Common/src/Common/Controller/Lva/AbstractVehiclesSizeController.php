<?php

declare(strict_types=1);

namespace Common\Controller\Lva;

use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\DataHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Script\ScriptFactory;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use LmcRbacMvc\Service\AuthorizationService;

abstract class AbstractVehiclesSizeController extends AbstractController
{
    public function __construct(
        NiTextTranslation $niTextTranslationUtil,
        AuthorizationService $authService,
        protected FormServiceManager $formServiceManager,
    ) {
        parent::__construct($niTextTranslationUtil, $authService);
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $isPost = $request->isPost();

        $data = $isPost ? $request->getPost() : $this->getFormData();

        $form = $this->formServiceManager->get('lva-' . $this->lva . '-vehicles_size')->getForm();
        $form->setData($data);

        if ($isPost && $form->isValid()) {
            $this->save($data);

            return $this->completeSection('vehicles_size');
        }

        return $this->render('vehicles_size', $form);
    }

    protected function getFormData(): array
    {
        return $this->formatDataForForm($this->loadData());
    }

    protected function loadData(): array
    {
        if ($this->data === null) {
            $response = $this->handleQuery(
                \Dvsa\Olcs\Transfer\Query\Application\VehicleSize::create(['id' => $this->getApplicationId()])
            );

            if (!$response->isOk()) {
                throw new \RuntimeException('Error getting vehicle declaration');
            }

            $this->data = $response->getResult();
        }

        return $this->data;
    }

    protected function formatDataForForm(array $data): array
    {
        return [
            'version' => $data['version'],
            'psvVehicleSize' => [
                'size' => $data['psvWhichVehicleSizes']['id'] ?? null,
            ],
        ];
    }

    protected function save(array $data): void
    {
        $saveData['version'] = $data['version'];
        $saveData['id'] = $this->getApplicationId();
        $saveData['psvVehicleSize'] = $data['psvVehicleSize']['size'];

        $response = $this->handleCommand(
            \Dvsa\Olcs\Transfer\Command\Application\UpdateVehicleSize::create($saveData)
        );

        if (!$response->isOk()) {
            throw new \RuntimeException('Error updating vehicle size');
        }
    }
}
