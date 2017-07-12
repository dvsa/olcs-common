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
    /**
     * Alter variation form
     *
     * @param Form  $form   Form class
     * @param array $params Parameters for form
     *
     * @return void
     */
    protected function alterForm(Form $form, array $params = [])
    {
        parent::alterForm($form, $params);

        $this->removeStandardFormActions($form);
    }
}
