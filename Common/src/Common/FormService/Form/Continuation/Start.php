<?php

namespace Common\FormService\Form\Continuation;

use Common\FormService\Form\AbstractFormService;
use Common\Form\Model\Form\Continuation\Start as StartForm;
use Common\Form\Form;

/**
 * Continuation start form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Start extends AbstractFormService
{
    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm(StartForm::class);

        return $form;
    }
}
