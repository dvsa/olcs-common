<?php

namespace Common\Controller\Lva\Adapters;

use Common\Service\Data\CategoryDataService as Category;
use Dvsa\Olcs\Transfer\Query\Application\FinancialEvidence;
use Common\RefData;

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{
    protected $applicationData = null; // cache

    /**
     * @param Common\Form\Form
     * @return void
     */
    public function alterFormForLva($form)
    {
        $form->get('finance')->get('requiredFinance')
            ->setValue('markup-required-finance-application');
    }

    /**
     * @param int $applicationId
     * @return array
     */
    public function getDocuments($applicationId)
    {
        $documents = $this->getData($applicationId)['documents'];

        return is_array($documents) ? $documents : [];
    }

    /**
     * @param array $file
     * @param int $applicationId
     * @return array
     */
    public function getUploadMetaData($file, $applicationId)
    {
        $licenceId = $this->getData($applicationId)['licence']['id'];

        return [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
            'licence'     => $licenceId,
        ];
    }

    /**
     * Single call to get all the application data from the backend, including
     * financial evidence data and documents.
     */
    public function getData($applicationId, $noCache = false)
    {
        if (is_null($this->applicationData) || $noCache) {
            $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
                ->createQuery(FinancialEvidence::create(['id' => $applicationId]));

            $response = $this->getServiceLocator()->get('QueryService')->send($query);

            $this->applicationData = $response->getResult();
        }
        return $this->applicationData;
    }
}
