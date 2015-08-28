<?php

/**
 * ConditionsUndertakings Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * ConditionsUndertakings Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ConditionsUndertakings extends AbstractFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\ConditionsUndertakings');

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $form;
    }
}
