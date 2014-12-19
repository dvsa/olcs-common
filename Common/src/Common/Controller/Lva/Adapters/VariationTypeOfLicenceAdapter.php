<?php

/**
 * Variation Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Type Of Licence Adapter
 * @NOTE This is a CONTROLLER adapter and thus contains logic similar to that of a controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTypeOfLicenceAdapter extends AbstractTypeOfLicenceAdapter
{
    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @param int $id
     * @param string $applicationType
     * @return \Zend\Form\Form
     */
    public function alterForm(\Zend\Form\Form $form, $id = null, $applicationType = null)
    {
        $applicationEntityService = $this->getServiceLocator()->get('Entity\Application');
        $licenceAdapter = $this->getServiceLocator()->get('LicenceTypeOfLicenceAdapter');
        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $licenceId = $applicationEntityService->getLicenceIdForApplication($id);

        $form = $licenceAdapter->alterForm($form, $licenceId, $applicationType);

        $typeOfLicenceData = $licenceEntityService->getTypeOfLicenceData($licenceId);

        $formHelper->setCurrentOption(
            $form->get('type-of-licence')->get('licence-type'),
            $typeOfLicenceData['licenceType']
        );

        return $form;
    }
}
