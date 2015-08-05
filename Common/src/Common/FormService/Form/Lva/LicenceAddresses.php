<?php

/**
 * Licence Addresses Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\Lva\Addresses;

/**
 * Licence Addresses Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class LicenceAddresses extends Addresses
{
    protected function alterForm($form, $params)
    {
        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);

        parent::alterForm($form, $params);
    }
}
