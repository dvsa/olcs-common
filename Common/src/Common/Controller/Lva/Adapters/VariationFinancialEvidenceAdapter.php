<?php

/**
 * Variation Type Of Variation Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class VariationFinancialEvidenceAdapter extends AbstractFinancialEvidenceAdapter
{
    public function getTotalNumberOfAuthorisedVehicles($id)
    {
        // @TODO
        return 0;
    }

    public function getRequiredFinance($id)
    {
        // @TODO
        return 0;
    }

    public function alterFormForLva($form)
    {
        $form->get('finance')->get('requiredFinance')
            ->setValue('markup-required-finance-variation');
    }
}
