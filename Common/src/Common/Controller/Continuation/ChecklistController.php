<?php

namespace Common\Controller\Continuation;

use Zend\View\Model\ViewModel;
use Dvsa\Olcs\Transfer\Query\ContinuationDetail\LicenceChecklist as LicenceChecklistQuery;
use Common\Data\Mapper\LicenceChecklist as LicenceChecklistMapper;

/**
 * ChecklistController
 */
class ChecklistController extends AbstractContinuationController
{
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

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $this->redirect()->toRoute('continuation/finances', [], [], true);
            }
        }

        return $this->getViewModel(
            $licenceData['licNo'],
            $form,
            LicenceChecklistMapper::mapFromResultToView($licenceData, $translator)
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
                'people' => $mappedData['people'],
                'totalPeopleMessage' => $mappedData['totalPeopleMessage'],
                'totalPeopleCount' => count($organisationUsers)
            ]
        );

        $view->setTemplate('pages/continuation-section');

        return $view;
    }
}
