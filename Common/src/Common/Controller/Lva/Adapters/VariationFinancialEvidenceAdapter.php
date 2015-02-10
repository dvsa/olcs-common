<?php

/**
 * Variation Type Of Variation Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Data\CategoryDataService as Category;

/**
 * Variation Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class VariationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{
    public function getTotalNumberOfAuthorisedVehicles($id)
    {
        return $this->getApplicationAdapter()->getTotalNumberOfAuthorisedVehicles($id);
    }

    /**
     * @todo
     */
    public function getRequiredFinance($id)
    {
        return $this->getApplicationAdapter()->getTotalNumberOfAuthorisedVehicles($id);
    }

    public function alterFormForLva($form)
    {
        $form->get('finance')->get('requiredFinance')
            ->setValue('markup-required-finance-variation');
    }

    public function getDocuments($applicationId)
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDocuments(
                $applicationId,
                Category::CATEGORY_APPLICATION,
                Category::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL
            );
    }

    public function getUploadMetaData($file, $applicationId)
    {
        return $this->getApplicationAdapter()->getUploadMetaData($file, $applicationId);
    }

    protected function getApplicationAdapter()
    {
        return $this->getServiceLocator()->get('ApplicationFinancialEvidenceAdapter');
    }
}
