<?php

/**
 * Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Licence Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Licence extends AbstractFormService
{
    public function alterForm($form)
    {
        $form->get('form-actions')->remove('saveAndContinue');
    }
}
