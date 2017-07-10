<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist as LicenceChecklistQuery;
use Common\Data\Mapper\Continuation\LicenceChecklist as LicenceChecklistMapper;

/**
 * ChecklistController
 */
class ChecklistController extends AbstractContinuationController
{
    protected $layout = 'pages/continuation-checklist';
    // @todo should be replaced with a correct layout
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
                $this->redirect()->toRoute('continuation/finances', [], [], true);
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
            // currently we assume that all server errors will be handled in one place
            // so we always should have data here.
            // just display and error in case of any client error
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
}
