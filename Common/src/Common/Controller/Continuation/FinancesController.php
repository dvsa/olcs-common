<?php

namespace Common\Controller\Continuation;

use Common\Data\Mapper\Continuation\Finances;
use Common\Form\Form;
use Common\Service\Helper\TranslationHelperService;
use Common\Util\TranslatorDelegator;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\UpdateFinances;
use Zend\View\Model\ViewModel;

/**
 * FinancesController
 */
class FinancesController extends AbstractContinuationController
{
    /**
     * Index page
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $continuationDetail = $this->getContinuationDetailData();

        $this->setGuidanceMessage($continuationDetail);

        $form = $this->getFinancesForm();

        $form->setData(Finances::mapFromResult($continuationDetail));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {

                $dtoData = array_merge(
                    Finances::mapFromForm($form->getData()),
                    ['id' => $continuationDetail['id']]
                );
                $response = $this->handleCommand(UpdateFinances::create($dtoData));
                if ($response->isOk()) {

                    $totalFunds = (float)$dtoData['averageBalanceAmount']
                        + (float)$dtoData['overdraftAmount']
                        + (float)$dtoData['otherFinancesAmount'];
                    if ($totalFunds >= (float)$continuationDetail['financeRequired']) {
                        $this->redirect()->toRoute('continuation/declaration', [], [], true);
                    }
                    $this->redirect()->toRoute('continuation/insufficent-finances', [], [], true);
                }
                $this->addErrorMessage('unknown-error');
            }
        }

        return $this->getViewModel($continuationDetail['licence']['licNo'], $form);
    }

    /**
     * Get form
     *
     * @return Form
     */
    protected function getFinancesForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm(
            \Common\Form\Model\Form\Continuation\Finances::class
        );
    }

    /**
     * Set the guidance message
     *
     * @param array $continuationDetail Continuation Detail data
     *
     * @return void
     */
    private function setGuidanceMessage($continuationDetail)
    {
        $financeRequired = number_format((int)$continuationDetail['financeRequired']);

        /** @var TranslationHelperService $translatorHelper */
        $translatorHelper = $this->getServiceLocator()->get('Helper\Translation');
        $guideMessage = $translatorHelper->translateReplace('continuations.finances.hint', [$financeRequired]);
        $this->getServiceLocator()->get('Helper\Guidance')->append($guideMessage);
    }
}
