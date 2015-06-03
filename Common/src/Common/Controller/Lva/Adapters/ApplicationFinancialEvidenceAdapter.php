<?php

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;
use Common\Service\Data\CategoryDataService as Category;
use Dvsa\Olcs\Transfer\Query\Application\FinancialEvidence;

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{

    protected $applicationData   = null; // cache

    /**
     * @param int $applicationId
     * @return array
     */
    public function getFormData($applicationId)
    {
        $applicationData = $this->getData($applicationId);

        $uploaded = $applicationData['financialEvidenceUploaded'];

        return [
            'id'       => $applicationId,
            'version'  => $applicationData['version'],
            'evidence' => [
                // default to Y
                'uploadNow' => is_null($uploaded) ? 'Y' : $uploaded,
            ],
        ];
    }

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
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments(
                $applicationId,
                Category::CATEGORY_APPLICATION,
                Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            );
    }

    /**
     * @param array $file
     * @param int $applicationId
     * @return array
     */
    public function getUploadMetaData($file, $applicationId)
    {
        $data = $this->getData($applicationId)['licence']['id'];

        return [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
            'licence'     => $licenceId,
        ];
    }

    /**
     * Get application data from the backend, included financial evidence data
     */
    public function getData($applicationId)
    {
        if (is_null($this->applicationData)) {
            $response = $this->getController()->handleQuery(FinancialEvidence::create(['id' => $applicationId]));
            $this->applicationData =  $response->getResult();
        }
        return $this->applicationData;
    }

}
