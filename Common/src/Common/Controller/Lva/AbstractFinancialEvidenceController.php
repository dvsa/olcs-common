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

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getFinancialEvidenceForm()->setData($data);

        $this->getAdapter()->alterFormForLva($form);

        $id = $this->getIdentifier();
        $adapter = $this->getAdapter();
        $hasProcessedFiles = $this->processFiles(
            $form,
            'upload->file',
            array($this, 'processFinancialEvidenceFileUpload'),
            array($this, 'deleteFile'),
            function() use ($id, $adapter) {
                return $adapter->getDocuments($id);
            }
        );

        if (!$hasProcessedFiles && $request->isPost() && $form->isValid()) {
            // @todo save the fact we have/haven't submitted evidence!
            $this->postSave('financial_evidence');
            return $this->completeSection('financial_evidence');
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['financial-evidence']);

        $variables = array_merge(
            [
                'vehicles' => $this->getAdapter()->getTotalNumberOfAuthorisedVehicles($id),
                'requiredFinance' => $this->getAdapter()->getRequiredFinance($id),
            ],
            $this->getAdapter()->getRatesForView()
        );

        return $this->render('financial_evidence', $form, $variables);
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

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    public function processFinancialEvidenceFileUpload($file)
    {
        $id = $this->getIdentifier('application');

        $this->uploadFile($file, $this->getAdapter()->getUploadMetaData($file, $id));
    }

    /**
     * @todo we need something in the application model to store the flag
     */
    protected function getFormData()
    {
       return [];
    }
}
