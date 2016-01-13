<?php

/**
 * Addresses Form
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Addresses Form
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Addresses extends AbstractFormService
{
    public function getForm($licenceType)
    {
        $form = $this->getFormHelper()->createForm('Lva\Addresses');

        $this->alterForm($form, $licenceType);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form, $licenceType)
    {
        $allowedLicTypes = array(
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        if (!in_array($licenceType, $allowedLicTypes)) {
            $this->getFormHelper()->remove($form, 'establishment')
                ->remove($form, 'establishment_address');
        }

        return $form;
    }
}
