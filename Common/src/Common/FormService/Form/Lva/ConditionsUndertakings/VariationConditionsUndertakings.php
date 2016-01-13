<?php

/**
 * Variation Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\ConditionsUndertakings;

/**
 * Variation Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakings extends AbstractConditionsUndertakings
{
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
