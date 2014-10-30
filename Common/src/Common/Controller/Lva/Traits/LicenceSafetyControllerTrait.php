<?php

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * Licence Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceSafetyControllerTrait
{
    protected function save($data)
    {
        // Get the (first/)licence part of the formatted data
        $licence = $this->formatSaveData($data)[0];

        $licence['id'] = $this->getLicenceId();

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
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'application->safetyConfirmation');
    }
}
