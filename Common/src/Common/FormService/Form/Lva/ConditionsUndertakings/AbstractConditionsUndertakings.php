<?php

/**
 * Abstract Conditions Undertakings
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva\ConditionsUndertakings;

use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * Abstract Conditions Undertakings
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConditionsUndertakings extends AbstractLvaFormService
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
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        return $form;
    }
}
