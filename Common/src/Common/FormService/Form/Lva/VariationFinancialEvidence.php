<?php

/**
 * Variation Financial Evidence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Financial Evidence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialEvidence extends FinancialEvidence
{
    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');

        return parent::alterForm($form);
    }
}
