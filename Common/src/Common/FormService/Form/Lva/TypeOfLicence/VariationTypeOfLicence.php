<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

/**
 * Variation Type Of Licence
 */
class VariationTypeOfLicence extends AbstractTypeOfLicence
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;
    protected FormServiceManager $formServiceLocator;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService, FormServiceManager $formServiceLocator)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
        $this->formServiceLocator = $formServiceLocator;
    }

    protected function alterForm(Form $form, $params = [])
    {
        parent::alterForm($form, $params);

        $this->formServiceLocator->get('lva-variation')->alterForm($form);

        $this->lockElements($form, $params);

        $licenceTypeFieldset = $form->get('type-of-licence')->get('licence-type');

        $this->formHelper->setCurrentOption(
            $licenceTypeFieldset->get('licence-type'),
            $params['currentLicenceType']
        );

        $this->formHelper->setCurrentOption(
            $licenceTypeFieldset->get('ltyp_siContent')->get('vehicle-type'),
            $params['currentVehicleType']
        );
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'variation');
    }
}
