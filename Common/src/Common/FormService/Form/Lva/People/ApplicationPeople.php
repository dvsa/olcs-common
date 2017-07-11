<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;

/**
 * Application People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPeople extends AbstractPeople
{
    protected $lva = 'application';

    /**
     * Alter form
     *
     * @param Form  $form   Form class
     * @param array $params Parameters for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params = [])
    {
        parent::alterForm($form, $params);

        $this->removeFormAction($form, 'cancel');
    }
}
