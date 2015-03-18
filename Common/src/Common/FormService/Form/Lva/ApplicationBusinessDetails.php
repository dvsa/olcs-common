<?php

/**
 * Application Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Application Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBusinessDetails extends AbstractBusinessDetails
{
    public function alterForm($form)
    {
        $this->getFormServiceLocator()->get('lva-application')->alterForm($form);

        parent::alterForm($form);
    }
}
