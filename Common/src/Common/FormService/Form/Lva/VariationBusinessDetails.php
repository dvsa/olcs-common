<?php

/**
 * Variation Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Business Details Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessDetails extends AbstractBusinessDetails
{
    public function alterForm($form)
    {
        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);

        parent::alterForm($form);
    }
}
