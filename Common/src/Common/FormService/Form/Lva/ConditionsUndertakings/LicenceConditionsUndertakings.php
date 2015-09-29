<?php

/**
 * Licence Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\ConditionsUndertakings;

/**
 * Licence Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakings extends AbstractConditionsUndertakings
{
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
