<?php

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;
use Common\Form\Model\Form\Licence\AddPerson;

/**
 * Licence People
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceAddPerson extends AbstractPeople
{
    /**
     * Get the form
     *
     * @param array $params params
     *
     * @return Form $form form
     */
    public function getForm(array $params = [])
    {
        $form = $this->getFormHelper()->createForm(AddPerson::class);
        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Alter licence add person form
     *
     * @param Form  $form   Form
     * @param array $params Parameters / options for form
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params = [])
    {
        $form = parent::alterForm($form, $params);

        return $form;
    }
}
