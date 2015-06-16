<?php

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva;

use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialEvidence;

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractFinancialEvidenceController extends AbstractController
{
    use Traits\AdapterAwareTrait;

    /**
     * Financial evidence section
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $id      = $this->getIdentifier();
        $adapter = $this->getAdapter();

        // get data
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $adapter->getFormData($id);
        }

        // set up form
        $form = $this->getFinancialEvidenceForm()->setData($data);
        $adapter->alterFormForLva($form);

        // handle files
        $hasProcessedFiles = $this->processFiles(
            $form,
            'evidence->files',
            array($this, 'processFinancialEvidenceFileUpload'),
            array($this, 'deleteFile'),
            array($this, 'getDocuments'),
            'evidence->uploadedFileCount'
        );

        if (!$hasProcessedFiles && $request->isPost() && $form->isValid()) {
            // update application record and redirect
            if ($this->saveFinancialEvidence($data)) {
                return $this->completeSection('financial_evidence');
            }
        }

        // load scripts
        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        // render view
        $financialEvidenceData = $adapter->getData($id);
        $variables = $financialEvidenceData['financialEvidence'];

        return $this->render('financial_evidence', $form, $variables);
    }

    /**
     * Callback to handle the file upload
     *
     * @param array $file
     */
    public function processFinancialEvidenceFileUpload($file)
    {
        $id = $this->getIdentifier();

        $data = array_merge(
            $this->getAdapter()->getUploadMetaData($file, $id),
            [
                'isExternal' => $this->isExternal()
            ]
        );

        $this->uploadFile($file, $data);
    }

    /**
     * Callback to get list of documents
     *
     * @return array
     */
    public function getDocuments()
    {
        $id = $this->getIdentifier();
        return $this->getAdapter()->getDocuments($id);
    }

    protected function saveFinancialEvidence($formData)
    {
        $dto = UpdateFinancialEvidence::create(
            [
                'id' => $this->getIdentifier(),
                'version' => $formData['version'],
                'financialEvidenceUploaded' => $formData['evidence']['uploadNow'],
            ]
        );

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if ($response->isOk()) {
            return true;
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
        return false;
    }

    /**
     * Prepare the financial evidence form
     *
     * @return \Zend\Form\Form
     */
    protected function getFinancialEvidenceForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\FinancialEvidence');
    }
}
