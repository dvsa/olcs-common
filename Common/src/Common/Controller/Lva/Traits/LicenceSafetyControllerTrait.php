<?php

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;
use Olcs\Controller\Lva\Traits\LicenceControllerTrait;

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceSafetyControllerTrait
{
    use LicenceControllerTrait {
        LicenceControllerTrait::alterFormForLva as genericAlterFormForLva;
    }

    protected function save($data)
    {
        // Get the (first/)licence part of the formatted data
        $licence = $this->formatSaveData($data)[0];

        $licence['id'] = $this->getLicenceId();

        // @todo We may need to set the value of suitable maintence, depending on the AC being clarified

        $this->getServiceLocator()->get('Entity\Licence')->save($licence);
    }

    /**
     * Get Safety Data
     *
     * @return array
     */
    protected function getSafetyData()
    {
        $licence = $this->getServiceLocator()->get('Entity\Licence')->getSafetyData($this->getLicenceId());

        return array(
            'version' => null,
            'safetyConfirmation' => null,
            'isMaintenanceSuitable' => $licence['isMaintenanceSuitable'],
            'licence' => $licence
        );
    }

    /**
     * Alter the form depending on the LVA type
     *
     * @param \Zend\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {
        $this->genericAlterFormForLva($form);
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'application->safetyConfirmation');
    }
}
