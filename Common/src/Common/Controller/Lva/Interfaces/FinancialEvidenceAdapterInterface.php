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
    public function getTotalNumberOfAuthorisedVehicles($id);

    public function getRequiredFinance($id);

    public function alterFormForLva($form);
}
