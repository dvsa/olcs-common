<?php

/**
 * Application Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\ConditionsUndertakings;

/**
 * Application Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakings extends AbstractConditionsUndertakings
{
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeFormAction($form, 'save');
        $this->removeFormAction($form, 'cancel');

        return $form;
    }
}
