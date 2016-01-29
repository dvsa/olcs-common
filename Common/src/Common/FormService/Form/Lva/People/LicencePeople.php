<?php

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePeople extends AbstractPeople
{
    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
