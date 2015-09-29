<?php

/**
 * Variation Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People\SoleTrader;

/**
 * Variation Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSoleTrader extends AbstractSoleTrader
{
    protected function alterForm($form, array $params)
    {
        $form = parent::alterForm($form, $params);

        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);

        return $form;
    }
}
