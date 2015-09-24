<?php

/**
 * Variation People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

/**
 * Variation People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPeople extends AbstractPeople
{
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
