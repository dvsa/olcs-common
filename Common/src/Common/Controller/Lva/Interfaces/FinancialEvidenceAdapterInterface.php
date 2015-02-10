<?php

/**
 * Financial Evidence Adapter Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Financial Evidence Adapter Interface
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
interface FinancialEvidenceAdapterInterface extends AdapterInterface
{
    public function getFormData($id);

    public function getTotalNumberOfAuthorisedVehicles($id);

    public function getRequiredFinance($id);

    public function getDocuments($id);

    public function getUploadMetaData($file, $id);

    public function getRatesForView($id);

    public function alterFormForLva($form);
}
