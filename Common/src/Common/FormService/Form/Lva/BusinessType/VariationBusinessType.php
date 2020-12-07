<?php

/**
 * Variation Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\BusinessType;

use Laminas\Form\Form;

/**
 * Variation Business Type Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationBusinessType extends AbstractBusinessType
{
    protected $lva = 'variation';

    protected function alterForm(Form $form, $params)
    {
        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);

        parent::alterForm($form, $params);
    }
}
