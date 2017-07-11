<?php

/**
 * Variation People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;

/**
 * Variation People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPeople extends AbstractPeople
{
    protected function alterForm(Form $form, array $params = [])
    {
        parent::alterForm($form, $params);

        $this->removeStandardFormActions($form);
    }
}
