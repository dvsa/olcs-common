<?php

/**
 * Variation Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Data\CategoryDataService as Category;

/**
 * Variation Financial Evidence Adapter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @note For brevity, this extends the Application version, not the Abstract
 */
class VariationFinancialEvidenceAdapter extends ApplicationFinancialEvidenceAdapter
{
    /**
     * @param Common\Form\Form
     * @return void
     */
    public function alterFormForLva($form)
    {
        $form->get('finance')->get('requiredFinance')
            ->setValue('markup-required-finance-variation');
    }
}
