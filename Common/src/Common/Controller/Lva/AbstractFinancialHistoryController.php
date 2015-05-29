<?php

/**
 * Financial History Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Service\Data\CategoryDataService;
use Common\Data\Mapper\Lva\FinancialHistory as FinancialHistoryMapper;
use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialHistory;
use Dvsa\Olcs\Transfer\Query\Application\FinancialHistory;

/**
 * Financial History Controller
 *
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

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getFinancialHistoryForm()->setData($data);

        $this->alterFormForLva($form);

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
                $this->postSave('financial_history');
                return $this->completeSection('financial_history');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('financial-history');

        return $this->render('financial_history', $form);
    }

    protected function getFinancialHistoryForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\FinancialHistory');
    }

    protected function getFormData()
    {
        $response = $this->getFinancialHistory();

        if ($response->isNotFound()) {
            return $this->notFoundAction();
        }

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
     * @return \Common\Service\Cqrs\Response
     */
    protected function getFinancialHistory()
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(FinancialHistory::create(['id' => $this->getIdentifier()]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }

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
     * @param array $file
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

    protected function saveFinancialHistory($form, $formData)
    {
        $dto = UpdateFinancialHistory::create(
            [
                'id' => $this->getIdentifier(),
                'version' => $formData['version'],
                'bankrupt' => $formData['bankrupt'],
                'liquidation' => $formData['liquidation'],
                'receivership' => $formData['receivership'],
                'administration' => $formData['administration'],
                'disqualified' => $formData['disqualified'],
                'insolvencyDetails' => $formData['insolvencyDetails'],
            ]
        );

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if ($response->isOk()) {
            return true;
        }

        if ($response->isClientError()) {

            $fields = [
                'bankrupt' => 'bankrupt',
                'liquidation' => 'liquidation',
                'receivership' => 'receivership',
                'administration' => 'administration',
                'disqualified' => 'disqualified',
                'insolvencyDetails' => 'insolvencyDetails'
            ];
            $this->mapErrors($form, $response->getResult()['messages'], $fields, 'data');
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
        return false;
    }

    protected function mapErrors($form, array $errors, array $fields, $fieldsetName)
    {
        $formMessages = [];

        foreach ($fields as $errorKey => $fieldName) {
            if (isset($errors[$errorKey])) {
                foreach ($errors[$errorKey] as $key => $message) {
                    $formMessages[$fieldsetName][$fieldName][] = $message;
                }

                unset($errors[$key]);
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
