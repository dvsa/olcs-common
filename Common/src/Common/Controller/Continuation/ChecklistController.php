<?php

namespace Common\Controller\Continuation;

use Common\Form\Model\Form\Continuation\LicenceChecklist as LicenceChecklistForm;
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
        $licenceData = $this->getLicenceData(
            $this->getContinuationDetailId()
        );

        $form = $this->getForm(LicenceChecklistForm::class);

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
     * Get licence data
     *
     * @param int $continuationDetailId continuation detail id
     *
     * @return array
     */
    protected function getLicenceData($continuationDetailId)
    {
        $dto = LicenceChecklistQuery::create(['id' => $continuationDetailId]);
        $response = $this->handleQuery($dto);
        if (!$response->isOk()) {
            // currently we assume that all server errors will be handled in one place
            // so we always should have data here.
            // just display and error in case of any client error
            $this->addErrorMessage('unknown-error');
        }
        return $response->getResult()['licence'];
    }
}
