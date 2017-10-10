<?php

namespace Common\FormService\Form\Lva\People;

use Common\Form\Form;
use Common\Form\Model\Form\Lva\People;
use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * People Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractPeople extends AbstractLvaFormService
{
    /**
     * Get People form
     *
     * @param array $params Parameters or options for form
     *
     * @return Form
     */
    public function getForm(array $params = [])
    {
        $form = $this->getFormHelper()->createForm(People::class);


        $this->alterForm($form, $params);

        return $form;
    }

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
        return $form;
    }
}
