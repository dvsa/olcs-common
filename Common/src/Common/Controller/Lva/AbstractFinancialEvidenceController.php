<?php

/**
 * Abstract Financial Evidence Controller
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva;

use Common\Service\Entity\LicenceEntityService as Licence;

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
            $this->saveData($id, $data);
            $this->postSave('financial_evidence');
            return $this->completeSection('financial_evidence');
        }

        // load scripts
        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        // render view
        $variables = array_merge(
            [
                'vehicles' => $adapter->getTotalNumberOfAuthorisedVehicles($id),
                'requiredFinance' => $adapter->getRequiredFinance($id),
            ],
            $adapter->getRatesForView($id)
        );

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

        $this->uploadFile($file, $this->getAdapter()->getUploadMetaData($file, $id));
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

    /**
     * @param int $id,
     * @param array $data
     */
    protected function saveData($id, $data)
    {
        $saveData = [
            'id' => $id,
            'version' => $data['version'],
            'financialEvidenceUploaded' => $data['evidence']['uploadNow'],
        ];
        $this->getServiceLocator()->get('Entity\Application')->save($saveData);
    }

    /**
     * Prepare the financial evidence form
     *
     * @return \Zend\Form\Form
     */
    private function getFinancialEvidenceForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\FinancialEvidence');

        return $form;
    }
}
