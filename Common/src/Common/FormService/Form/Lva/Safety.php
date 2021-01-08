<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Safety Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Safety extends AbstractFormService
{
    /**
     * Returns form
     *
     * @return \Laminas\Form\Form
     */
    public function getForm()
    {
        return $this->getFormHelper()->createForm('Lva\Safety');
    }
}
