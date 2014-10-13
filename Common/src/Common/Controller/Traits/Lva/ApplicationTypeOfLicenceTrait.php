<?php

/**
 * Application Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Application Type Of Licence Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationTypeOfLicenceTrait
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

            return $this->completeSection('type_of_licence');
        }

        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->render('type_of_licence', $form);
    }

    /**
     * Create application action
     */
    protected function createApplicationAction()
    {
        if ($this->isButtonPressed('cancel')) {
            return $this->redirect()->toRoute('dashboard');
        }

        $request = $this->getRequest();

        $form = $this->getTypeOfLicenceForm();
        $form->get('form-actions')->remove('saveAndContinue')
            ->get('save')->setLabel('continue.button');

        if ($request->isPost()) {
            $data = (array)$request->getPost();

            $form->setData($data);

            if ($form->isValid()) {

                $organisationId = $this->getCurrentOrganisationId();
                $ids = $this->getEntityService('Application')->createNew($organisationId);

                $data = $this->formatDataForSave($data);

                $data['id'] = $ids['licence'];
                $data['version'] = 1;

                $this->getEntityService('Licence')->save($data);

                $this->updateCompletionStatuses($ids['application']);

                return $this->goToOverview($ids['application']);
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->renderCreateApplication('type_of_licence', $form);
    }
}
