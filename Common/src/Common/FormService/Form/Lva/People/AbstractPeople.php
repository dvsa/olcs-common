<?php

/**
 * People Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People;

use Common\FormService\Form\Lva\AbstractLvaFormService;

/**
 * People Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractPeople extends AbstractLvaFormService
{
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm('Lva\People');

        $this->alterForm($form);

        return $form;
    }

    protected function alterForm($form)
    {
        // No op
        return $form;
    }
}
