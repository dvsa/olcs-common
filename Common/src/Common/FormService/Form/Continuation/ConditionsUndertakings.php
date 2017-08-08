<?php

namespace Common\FormService\Form\Continuation;

use Common\FormService\Form\AbstractFormService;
use Common\Form\Model\Form\Continuation\ConditionsUndertakings as ConditionsUndertakingsForm;
use Common\Form\Form;

/**
 * Continuation conditions / undertakings form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ConditionsUndertakings extends AbstractFormService
{
    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm(ConditionsUndertakingsForm::class);

        return $form;
    }
}
