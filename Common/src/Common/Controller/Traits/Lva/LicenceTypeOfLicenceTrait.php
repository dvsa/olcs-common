<?php

/**
 * Licence Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Licence Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceTypeOfLicenceTrait
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
        $form->get('form-actions')->remove('saveAndContinue');

        if ($request->isPost() && $form->isValid()) {

            $licenceId = $this->getLicenceId();

            $data = $this->formatDataForSave($data);

            $data['id'] = $licenceId;

            $this->getEntityService('Licence')->save($data);

            $this->addSectionUpdatedMessage('type_of_licence');

            return $this->goToOverviewAfterSave($licenceId);
        }

        return $this->render($this->getSectionView($form));
    }
}
