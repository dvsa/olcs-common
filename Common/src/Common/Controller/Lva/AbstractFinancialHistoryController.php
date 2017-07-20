<?php

/**
 * Financial History Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Data\CategoryDataService;
use Common\Data\Mapper\Lva\FinancialHistory as FinancialHistoryMapper;
use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialHistory;
use Dvsa\Olcs\Transfer\Query\Application\FinancialHistory;
use Zend\Form\Form;
use Zend\Form\FormInterface;

/**
 * Financial History Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractFinancialHistoryController extends AbstractController
{
    public $financialHistoryDocuments = [];

    /**
     * Map the data
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'data'
            )
        )
    );

    /**
     * Process Action - Index
     *
     * @return \Common\View\Model\Section|\Zend\Http\Response
     */
    public function indexAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getFinancialHistoryForm()->setData($data);
        $this->alterFormForLva($form, $data);

        $hasProcessedFiles = $this->processFiles(
            $form,
            'data->file',
            array($this, 'processFinancialFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        if (!$hasProcessedFiles && $request->isPost() && $form->isValid()) {

            $data = $this->getServiceLocator()->get('Helper\Data')->processDataMap($data, $this->dataMap);

            if ($this->saveFinancialHistory($form, $data)) {
                return $this->completeSection('financial_history');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('financial-history');

        return $this->render('financial_history', $form);
    }

    /**
     * Alter form for LVA form
     *
     * @param Form  $form Form
     * @param array $data Api/Form Data
     *
     * @return Form
     */
    protected function alterFormForLva(Form $form, $data = null)
    {
        $this->updateInsolvencyConfirmationLabel($form, $data);
        return $form;
    }

    /**
     * If the licence is NI then update the label.  Used in current controller
     * and CommonVariationControllerTrait.
     *
     * @param Form  $form Form
     * @param array $data Api/Form Data
     *
     * @return Form
     */
    protected function updateInsolvencyConfirmationLabel(Form $form, $data = null)
    {
        if (isset($data['data']['niFlag']) && $data['data']['niFlag'] === 'Y') {
            $form->get('data')
                ->get('insolvencyConfirmation')
                ->setLabel('application_previous-history_financial-history.insolvencyConfirmation.title.ni');
        }

        return $form;
    }

    /**
     * Get Financial History Form
     *
     * @return FormInterface
     */
    protected function getFinancialHistoryForm()
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-financial_history')
            ->getForm($this->getRequest());
    }

    /**
     * Get Form Data
     *
     * @return array
     */
    protected function getFormData()
    {
        $response = $this->getFinancialHistory();

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        $mappedResults = [];
        if ($response->isOk()) {
            $mapper = new FinancialHistoryMapper();
            $mappedResults = $mapper->mapFromResult($response->getResult());
            $this->financialHistoryDocuments = $mappedResults['data']['documents'];
        }
        return $mappedResults;
    }

    /**
     * Get Hinancial History
     *
     * @return \Common\Service\Cqrs\Response
     */
    protected function getFinancialHistory()
    {
        return $this->handleQuery(FinancialHistory::create(['id' => $this->getIdentifier()]));
    }

    /**
     * Get Documents
     *
     * @return array
     */
    public function getDocuments()
    {
        if (!$this->financialHistoryDocuments) {
            // need this just to populate documents list after upload
            $this->getFormData();
        }
        return $this->financialHistoryDocuments;
    }

    /**
     * Handle the file upload
     *
     * @param array $file File
     *
     * @return void
     */
    public function processFinancialFileUpload($file)
    {
        $this->uploadFile(
            $file,
            array(
                'application' => $this->getApplicationId(),
                'description' => 'Insolvency document',
                'category'    => CategoryDataService::CATEGORY_LICENSING,
                'subCategory' => CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL,
                'licence'     => $this->getLicenceId(),
                'isExternal'  => $this->isExternal()
            )
        );
    }

    /**
     * Save Financial History
     *
     * @param Form  $form     Form
     * @param array $formData Form Data
     *
     * @return bool
     */
    protected function saveFinancialHistory($form, $formData)
    {
        $dtoData = [
            'id' => $this->getIdentifier(),
            'version' => $formData['version'],
            'bankrupt' => $formData['bankrupt'],
            'liquidation' => $formData['liquidation'],
            'receivership' => $formData['receivership'],
            'administration' => $formData['administration'],
            'disqualified' => $formData['disqualified'],
            'insolvencyDetails' => $formData['insolvencyDetails'],
            'insolvencyConfirmation' => $formData['insolvencyConfirmation']
        ];

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand(UpdateFinancialHistory::create($dtoData));

        if ($response->isOk()) {
            return true;
        }

        if ($response->isClientError()) {
            $this->mapErrors($form, $response->getResult()['messages']);
            return false;
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        return false;
    }

    /**
     * Map Errors
     *
     * @param Form  $form   Form
     * @param array $errors Errors
     *
     * @return void
     */
    protected function mapErrors($form, array $errors)
    {
        $formMessages = [];

        $fields = [
            'bankrupt' => 'bankrupt',
            'liquidation' => 'liquidation',
            'receivership' => 'receivership',
            'administration' => 'administration',
            'disqualified' => 'disqualified',
            'insolvencyDetails' => 'insolvencyDetails',
            'insolvencyConfirmation' => 'insolvencyConfirmation'
        ];

        foreach ($fields as $errorKey => $fieldName) {
            if (isset($errors[$errorKey])) {
                foreach ($errors[$errorKey] as $message) {
                    $formMessages['data'][$fieldName][] = $message;
                }

                unset($errors[$errorKey]);
            }
        }

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
