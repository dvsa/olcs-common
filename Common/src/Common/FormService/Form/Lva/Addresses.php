<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;

/**
 * Addresses Form
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Addresses extends AbstractFormService
{
    private static $establishmentAllowedLicTypes = [
        RefData::LICENCE_TYPE_STANDARD_NATIONAL,
        RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
    ];

    /**
     * Return form
     *
     * @param array $params Parameters
     *
     * @return \Zend\Form\Form
     */
    public function getForm(array $params)
    {
        $form = $this->getFormHelper()->createForm('Lva\Addresses');

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form   Form
     * @param array           $params Parameters
     *
     * @return void
     */
    protected function alterForm(\Zend\Form\Form $form, array $params)
    {
        $this->removeEstablishment($form, $params['typeOfLicence']['licenceType']);
    }

    /**
     * Remove Establishment Fields
     *
     * @param \Zend\Form\Form $form        Form
     * @param string          $licenceType Licence type
     *
     * @return void
     */
    protected function removeEstablishment(\Zend\Form\Form $form, $licenceType)
    {
        if (!in_array($licenceType, self::$establishmentAllowedLicTypes, true)) {
            $this->getFormHelper()
                ->remove($form, 'establishment')
                ->remove($form, 'establishment_address');
        }
    }
}
