<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist as LicenceChecklistQuery;
use Common\Data\Mapper\Continuation\LicenceChecklist as LicenceChecklistMapper;
use Common\RefData;

/**
 * ChecklistController
 */
class ChecklistController extends AbstractContinuationController
{
    const FINANCES_ROUTE = 'continuation/finances';
    const DECLARATION_ROUTE = 'continuation/declaration';

    protected $layout = 'pages/continuation-checklist';
    protected $checklistSectionLayout = 'layouts/simple';

    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];

        $form = $this->getForm('continuations-checklist', $data);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData((array) $request->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute($this->getNextStepRoute($licenceData), [], [], true);
            }
        }

        return $this->getViewModel(
            $licenceData['licNo'],
            $form,
            LicenceChecklistMapper::mapFromResultToView($data, $translator)
        );
    }

    /**
     * Get data
     *
     * @param int $continuationDetailId continuation detail id
     *
     * @return array
     */
    protected function getData($continuationDetailId)
    {
        $dto = LicenceChecklistQuery::create(['id' => $continuationDetailId]);
        $response = $this->handleQuery($dto);
        if (!$response->isOk()) {
            $this->addErrorMessage('unknown-error');
        }
        return $response->getResult();
    }

    /**
     * People section page
     *
     * @return ViewModel
     */
    public function peopleAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];
        $organisation = $data['licence']['organisation'];
        $organisationUsers = $organisation['organisationPersons'];
        $mappedData = LicenceChecklistMapper::mapPeopleSectionToView(
            $organisationUsers,
            $organisation['type']['id'],
            $translator
        );
        $view = new ViewModel(
            [
                'licNo' => $licenceData['licNo'],
                'data' => $mappedData['people'],
                'totalMessage' => $mappedData['totalPeopleMessage'],
                'totalCount' => count($organisationUsers)
            ]
        );

        return $this->renderSection($view);
    }

    /**
     * Vehicles section page
     *
     * @return ViewModel
     */
    public function vehiclesAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];
        $licenceVehicles = $licenceData['licenceVehicles'];
        $mappedData = LicenceChecklistMapper::mapVehiclesSectionToView(
            $licenceData,
            $translator
        );
        $view = new ViewModel(
            [
                'licNo' => $licenceData['licNo'],
                'data' => $mappedData['vehicles'],
                'totalMessage' => $mappedData['totalVehiclesMessage'],
                'totalCount' => count($licenceVehicles)
            ]
        );

        return $this->renderSection($view);
    }

    /**
     * Operating centres section page
     *
     * @return ViewModel
     */
    public function operatingCentresAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];
        $licenceVehicles = $licenceData['operatingCentres'];
        $mappedData = LicenceChecklistMapper::mapOperatingCentresSectionToView(
            $licenceData,
            $translator
        );
        $view = new ViewModel(
            [
                'licNo' => $licenceData['licNo'],
                'data' => $mappedData['operatingCentres'],
                'totalMessage' => $mappedData['totalOperatingCentresMessage'],
                'totalCount' => count($licenceVehicles)
            ]
        );

        return $this->renderSection($view);
    }

    /**
     * Transport managers section page
     *
     * @return ViewModel
     */
    public function transportManagersAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];
        $tmLicences = $licenceData['tmLicences'];
        $mappedData = LicenceChecklistMapper::mapTransportManagerSectionToView(
            $licenceData,
            $translator
        );
        $view = new ViewModel(
            [
                'licNo' => $licenceData['licNo'],
                'data' => $mappedData['transportManagers'],
                'totalMessage' => $mappedData['totalTransportManagersMessage'],
                'totalCount' => count($tmLicences)
            ]
        );

        return $this->renderSection($view);
    }

    /**
     * Safety inspectors section page
     *
     * @return ViewModel
     */
    public function safetyInspectorsAction()
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $data = $this->getData(
            $this->getContinuationDetailId()
        );
        $licenceData = $data['licence'];
        $mappedData = LicenceChecklistMapper::mapSafetyInspectorsSectionToView(
            $licenceData,
            $translator
        );
        $workshops = $licenceData['workshops'];
        $view = new ViewModel(
            [
                'licNo' => $licenceData['licNo'],
                'data' => $mappedData['safetyInspectors'],
                'totalMessage' => $mappedData['totalSafetyInspectorsMessage'],
                'totalCount' => count($workshops)
            ]
        );

        return $this->renderSection($view);
    }

    /**
     * Render section
     *
     * @param ViewModel $view view model
     *
     * @return ViewModel
     */
    protected function renderSection($view)
    {
        $view->setTemplate('pages/continuation-section');
        $base = new ViewModel();
        $base->setTemplate($this->checklistSectionLayout)
            ->setTerminal(true)
            ->addChild($view, 'content');

        return $base;
    }

    /**
     * Get next step route
     *
     * @param array $licenceData licence data
     *
     * @return string
     */
    protected function getNextStepRoute($licenceData)
    {
        if (
            $licenceData['licenceType']['id'] === RefData::LICENCE_TYPE_SPECIAL_RESTRICTED
            && $licenceData['goodsOrPsv']['id'] === RefData::LICENCE_CATEGORY_PSV
        ) {
            return self::DECLARATION_ROUTE;
        }
        return self::FINANCES_ROUTE;

    }
}
