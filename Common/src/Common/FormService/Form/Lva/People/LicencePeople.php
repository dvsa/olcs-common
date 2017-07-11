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
    protected function alterForm(Form $form, array $params = [])
    {
        $form = parent::alterForm($form, $params);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
