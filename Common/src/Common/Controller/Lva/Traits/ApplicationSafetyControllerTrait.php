<?php

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationSafetyControllerTrait
{
    /**
     * Save the form data
     *
     * @param array $data
     */
    protected function save($data)
    {
        list($licence, $application) = $this->formatSaveData($data);

        $licence['id'] = $this->getLicenceId();
        $application['id'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\Licence')->save($licence);
        $this->getServiceLocator()->get('Entity\Application')->save($application);
    }

    /**
     * Get Safety Data
     *
     * @return array
     */
    protected function getSafetyData()
    {
        return $this->getServiceLocator()->get('Entity\Application')->getSafetyData($this->getApplicationId());
    }

    /**
     * Alter the form depending on the LVA type
     *
     * @param \Zend\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {

    }
}
