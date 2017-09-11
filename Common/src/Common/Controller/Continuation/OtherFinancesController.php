<?php

namespace Common\Controller\Continuation;

use Common\Data\Mapper\Continuation\OtherFinances;
use Common\Form\Form;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\UpdateFinances;
use Zend\View\Model\ViewModel;

/**
 * OtherFinancesController
 */
class OtherFinancesController extends AbstractContinuationController
{
    protected $currentStep = self::STEP_FINANCE;

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

        $form->setData(OtherFinances::mapFromResult($continuationDetail));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {

                $dtoData = array_merge(
                    OtherFinances::mapFromForm($form->getData()),
                    ['id' => $continuationDetail['id']]
                );
                $response = $this->handleCommand(UpdateFinances::create($dtoData));
                if ($response->isOk()) {

                    $totalFunds = (float)$continuationDetail['averageBalanceAmount']
                        + (float)$continuationDetail['overdraftAmount']
                        + (float)$continuationDetail['factoringAmount']
                        + (float)$dtoData['otherFinancesAmount'];

                    if ($totalFunds >= (float)$continuationDetail['financeRequired']) {
                        return $this->redirect()->toRoute('continuation/declaration', [], [], true);
                    }
                    return $this->redirect()->toRoute('continuation/insufficient-finances', [], [], true);
                }
                $this->addErrorMessage('unknown-error');
            }
        }

        $vars = [
            'backRoute' => 'continuation/finances',
        ];
        return $this->getViewModel($continuationDetail['licence']['licNo'], $form, $vars);
    }

    /**
     * Get form
     *
     * @return Form
     */
    protected function getFinancesForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm(
            \Common\Form\Model\Form\Continuation\OtherFinances::class
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
        $shortByAmount = number_format(
            (float)$continuationDetail['financeRequired'] -
            (float)$continuationDetail['averageBalanceAmount'] -
            (float)$continuationDetail['overdraftAmount'] -
            (float)$continuationDetail['factoringAmount'],
            2
        );

        /** @var TranslationHelperService $translatorHelper */
        $translatorHelper = $this->getServiceLocator()->get('Helper\Translation');
        $guideMessage = $translatorHelper->translateReplace(
            'continuations.other-finances.hint',
            [$financeRequired, $shortByAmount]
        );
        $this->getServiceLocator()->get('Helper\Guidance')->append($guideMessage);
    }
}
