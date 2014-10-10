<?php

/**
 * Variation Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Variation Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationTypeOfLicenceTrait
{
    /**
     * Type of licence section
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getTypeOfLicenceData());
        }

        $form = $this->getTypeOfLicenceForm()->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $applicationId = $this->getApplicationId();

            $licenceId = $this->getLicenceId($applicationId);

            $data = $this->formatDataForSave($data);

            $data['id'] = $licenceId;

            $this->getEntityService('Licence')->save($data);

            $this->addSectionUpdatedMessage('type_of_licence');

            if ($this->isButtonPressed('saveAndContinue')) {
                return $this->goToNextSection('type_of_licence');
            }

            return $this->goToOverviewAfterSave($applicationId);
        }

        return $this->render($this->getSectionView($form));
    }
}
