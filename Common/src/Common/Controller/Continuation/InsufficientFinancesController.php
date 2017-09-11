<?php

namespace Common\Controller\Continuation;

use Common\Category;
use Common\Data\Mapper\Continuation\InsufficientFinances;
use Common\Form\Form;
use Common\Service\Helper\TranslationHelperService;
use Dvsa\Olcs\Transfer\Command\ContinuationDetail\UpdateInsufficientFinances;
use Zend\View\Model\ViewModel;

/**
 * InsufficientFinancesController
 */
class InsufficientFinancesController extends AbstractContinuationController
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

        $form = $this->getInsufficientFinancesForm();
        $form->setData(InsufficientFinances::mapFromResult($continuationDetail));

        $hasProcessedFiles = $this->processFiles(
            $form,
            'insufficientFinances->yesContent->uploadContent',
            array($this, 'processFinancialFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if (!$hasProcessedFiles && $form->isValid()) {
                $dtoData = array_merge(
                    InsufficientFinances::mapFromForm($form->getData()),
                    ['id' => $continuationDetail['id']]
                );

                $response = $this->handleCommand(UpdateInsufficientFinances::create($dtoData));
                if ($response->isOk()) {
                    $this->redirect()->toRoute('continuation/declaration', [], [], true);
                }
            }
        }

        $vars = [
            'continuationData' => [
                'Average balance' => $continuationDetail['averageBalanceAmount'],
                'Overdraft limit' => $continuationDetail['overdraftAmount'],
                'Factoring or discount facilities' => $continuationDetail['factoringAmount'],
                'Other available finances' => $continuationDetail['otherFinancesAmount'],
            ],
            'backRoute' => 'continuation/finances',
            'isNi' => $continuationDetail['licence']['trafficArea']['isNi'],
        ];

        return $this->getViewModel($continuationDetail['licence']['licNo'], $form, $vars);
    }

    /**
     * Get form
     *
     * @return Form
     */
    protected function getInsufficientFinancesForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm(
            \Common\Form\Model\Form\Continuation\InsufficientFinances::class
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
        /** @var TranslationHelperService $translatorHelper */
        $translatorHelper = $this->getServiceLocator()->get('Helper\Translation');
        $guideMessage = $translatorHelper->translate('continuations.insufficient-finances.hint');
        $this->getServiceLocator()->get('Helper\Guidance')->append($guideMessage);
    }

    /**
     * Process uploading of files
     *
     * @param array $file Uploaded file info
     *
     * @return void
     */
    public function processFinancialFileUpload($file)
    {
        $continuationDetail = $this->getContinuationDetailData();
        $this->uploadFile(
            $file,
            array(
                'continuationDetail' => $this->getContinuationDetailId(),
                'description' => $file['name'],
                'category'    => Category::CATEGORY_LICENSING,
                'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS,
                'licence'     => $continuationDetail['licence']['id'],
                'isExternal'  => true
            )
        );

        $this->getContinuationDetailData(true);
    }

    /**
     * Get list of uploaded files
     *
     * @return array
     */
    public function getDocuments()
    {
        $continuationDetail = $this->getContinuationDetailData();
        return $continuationDetail['documents'];
    }
}
