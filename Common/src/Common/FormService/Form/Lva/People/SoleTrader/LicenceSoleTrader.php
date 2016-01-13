<?php

/**
 * Licence Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\People\SoleTrader;

/**
 * Licence Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceSoleTrader extends AbstractSoleTrader
{
    protected function alterForm($form, array $params)
    {
        $form = parent::alterForm($form, $params);

        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        return $form;
    }
}
