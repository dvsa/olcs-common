<?php

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePeople extends AbstractPeople
{
    /**
     * Alter licence people form
     *
     * @param Form  $form   Form
     * @param array $params Parameters / options for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params = [])
    {
        $form = parent::alterForm($form, $params);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
