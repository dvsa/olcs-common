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
    public $financialHistoryDocuments;
    /**
     * Map the data
     *
     * @var array
     */
    /*
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

            $this->getServiceLocator()->get('Entity\Application')->save($data);

            $this->postSave('financial_history');

            return $this->completeSection('financial_history');
        }

        $this->getServiceLocator()->get('Script')->loadFile('financial-history');

        return $this->render('financial_history', $form);
    }

*/
    
    /**
     * Financial history section
     *
     * @return Response
     */
    public function indexAction()
    {
        $prg = $this->prg();

        // If have posted, and need to redirect to get
        if ($prg instanceof Response) {
            return $prg;
        }

        $form = $this->getFinancialHistoryForm();

        $this->alterFormForLva($form);

        // If we have no data (not posted)
        if ($prg === false) {

            $response = $this->getFinancialHistory();

            if ($response->isNotFound()) {
                return $this->notFoundAction();
            }

            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            if ($response->isOk()) {
                $mapper = new FinancialHistoryMapper();
                $mappedResults = $mapper->mapFromResult($response->getResult());
                $this->financialHistoryDocuments = $mappedResults['data']['documents'];
                $form->setData($mappedResults);
            }

            return $this->renderIndex($form);
        }

        $hasProcessedFiles = $this->processFiles(
            $form,
            'data->file',
            array($this, 'processFinancialFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments')
        );

        // If we have posted and have data
        $form->setData($prg);

        // If the form is invalid, render the errors
        if (!$form->isValid()) {
            return $this->renderIndex($form);
        }

        $formData = $form->getData();

        if (!$hasProcessedFiles) {
            $dto = UpdateFinancialHistory::create(
                [
                    'id' => $this->getIdentifier(),
                    'version' => $formData['version'],
                    'bankrupt' => $formData['data']['bankrupt'],
                    'liquidation' => $formData['data']['liquidation'],
                    'receivership' => $formData['data']['receivership'],
                    'administration' => $formData['data']['administration'],
                    'disqualified' => $formData['data']['disqualified'],
                    'insolvencyDetails' => $formData['data']['insolvencyDetails'],
                ]
            );

            $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

            /** @var \Common\Service\Cqrs\Response $response */
            $response = $this->getServiceLocator()->get('CommandService')->send($command);

            if ($response->isOk()) {
                return $this->completeSection('financial_history', $prg);
            }

            if ($response->isClientError()) {

                // This means we need confirmation
                if (isset($response->getResult()['messages']['AP-TOL-5'])) {

                    $query = $formData['type-of-licence'];
                    $query['version'] = $formData['version'];

                    return $this->redirect()->toRoute(
                        null,
                        ['action' => 'confirmation'],
                        ['query' => $query],
                        true
                    );
                }

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
        }
        return $this->renderIndex($form);
    }

    protected function getFinancialHistoryForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\FinancialHistory');
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    protected function getFinancialHistory()
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(Application::create(['id' => $this->getIdentifier()]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }
    
    
    protected function renderIndex($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('financial-history');

        return $this->render('financial_history', $form);
    }

    protected function mapErrors(Form $form, array $errors, array $fields, $fieldsetName)
    {
        $formMessages = [];
        
        foreach ($fields as $errorKey => $fieldName) {
            if (isset($errors[$errorKey])) {

                foreach ($errors[$errorKey][0] as $key => $message) {
                    $formMessages[$fieldsetName][$fieldName][] = $key;
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

    /*
    protected function getFormData()
    {
        $data = $this->getServiceLocator()->get('Entity\Application')
            ->getFinancialHistoryData($this->getApplicationId());

        return array(
            'data' => $data
        );
    }
    
    public function getDocuments()
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments(
                $this->getApplicationId(),
                CategoryDataService::CATEGORY_LICENSING,
                CategoryDataService::DOC_SUB_CATEGORY_LICENCE_INSOLVENCY_DOCUMENT_DIGITAL
            );
    }
    */
    public function getDocuments()
    {
        return $this->financialHistoryDocuments;
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processFinancialFileUpload($file)
    {
        $categoryService = $this->getServiceLocator()->get('category');

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
}
