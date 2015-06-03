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

/**
 * Application Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{

    protected $applicationData   = null; // cache

    protected $otherLicences     = null; // cache

    protected $otherApplications = null; // cache

    /**
     * @param int $applicationId
     * @return array
     */
    public function getFormData($applicationId)
    {
        $applicationData = $this->getApplicationData($applicationId);

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
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        return [
            'application' => $applicationId,
            'description' => $file['name'],
            'category'    => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL,
            'licence'     => $licenceId,
        ];
    }


    /**
     * @param int $applicationId
     * @return array
     */
    protected function getApplicationData($applicationId)
    {
        if (is_null($this->applicationData)) {
            $this->applicationData = $this->getServiceLocator()->get('Entity\Application')
                ->getDataForFinancialEvidence($applicationId);
        }
        return $this->applicationData;
    }
}
