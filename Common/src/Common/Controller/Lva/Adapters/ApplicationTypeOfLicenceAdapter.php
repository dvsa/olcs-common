<?php

/**
 * Application Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\ApplicationCompletionEntityService;

/**
 * Application Type Of Licence Adapter
 * @NOTE This is a CONTROLLER adapter and thus contains logic similar to that of a controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceAdapter extends AbstractTypeOfLicenceAdapter
{
    protected $confirmationMessage = 'application_type_of_licence_confirmation';
    protected $extraConfirmationMessage = 'application_type_of_licence_confirmation_subtitle';

    public function doesChangeRequireConfirmation(array $postData, array $currentData)
    {
        if (!$this->isCurrentDataSet($currentData)) {
            return false;
        }

        $this->queryParams = $postData;

        if ($this->queryParams['operator-location'] !== $currentData['niFlag']) {
            return true;
        }

        if ($this->queryParams['operator-type'] !== $currentData['goodsOrPsv']) {
            return true;
        }

        // If we have changed to or from special restricted
        if ($this->queryParams['licence-type'] !== $currentData['licenceType']
            && ($this->queryParams['licence-type'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                || $currentData['licenceType'] === LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED)) {

            return true;
        }

        return false;
    }

    public function processChange(array $postData, array $currentData)
    {
        if (!$this->isCurrentDataSet($currentData)) {
            return false;
        }

        // If we haven't changed licence type, do nothing
        if ($postData['licence-type'] === $currentData['licenceType']) {
            return false;
        }

        $applicationId = $this->getController()->params('application');

        $updatedApplicationData = ['licenceType' => $postData['licence-type']];

        $this->getServiceLocator()->get('Entity\Application')->forceUpdate($applicationId, $updatedApplicationData);

        $this->cancelFees($applicationId);
        $this->createFee($applicationId);

        $this->resetSectionStatuses($applicationId);

        return true;
    }

    public function processFirstSave($applicationId)
    {
        $this->createFee($applicationId);
    }

    public function createFee($applicationId)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        $this->getServiceLocator()->get('Processing\Application')
            ->createFee($applicationId, $licenceId, FeeTypeDataService::FEE_TYPE_APP);
    }

    public function confirmationAction()
    {
        $request = $this->getController()->getRequest();

        if ($request->isPost()) {

            $applicationId = $this->getController()->params('application');
            $applicationService = $this->getServiceLocator()->get('Entity\Application');

            $query = (array)$this->getController()->params()->fromQuery();
            $currentData = $applicationService->getTypeOfLicenceData($applicationId);

            if (!$this->doesChangeRequireConfirmation($query, $currentData)) {

                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addWarningMessage('tol-no-changes-message');

                return $this->getController()->redirect()->toRoute(null, ['action' => null], [], true);
            }

            $organisation = $applicationService->getOrganisation($applicationId);

            $this->removeApplication($applicationId);

            $newApplicationId = $this->createApplication($organisation['id'], $query);

            $this->createFee($newApplicationId);

            return $this->getController()->redirect()->toRouteAjax('lva-application', ['application' => $newApplicationId]);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('GenericConfirmation');
        $formHelper->setFormActionFromRequest($form, $this->getController()->getRequest());

        return $form;
    }

    protected function resetSectionStatuses($applicationId)
    {
        $applicationCompletionService = $this->getServiceLocator()->get('Entity\ApplicationCompletion');

        $applicationCompletion = $applicationCompletionService->getCompletionStatuses($applicationId);

        foreach ($applicationCompletion as $field => $value) {
            if ($value === ApplicationCompletionEntityService::STATUS_COMPLETE
                && preg_match('/[a-zA-Z]+Status/', $field)) {
                $applicationCompletion[$field] = ApplicationCompletionEntityService::STATUS_INCOMPLETE;
            }
        }

        $applicationCompletion['typeOfLicenceStatus'] = ApplicationCompletionEntityService::STATUS_COMPLETE;

        $applicationCompletionService->save($applicationCompletion);
    }

    protected function removeApplication($applicationId)
    {
        $this->getServiceLocator()->get('Entity\Task')->deleteList(['application' => $applicationId]);
        $this->getServiceLocator()->get('Entity\Application')->delete($applicationId);
    }

    protected function createApplication($organisationId, $typeOfLicence)
    {
        $newApplicationData = array(
            'niFlag' => $typeOfLicence['operator-location'],
            'goodsOrPsv' => $typeOfLicence['operator-type'],
            'licenceType' => $typeOfLicence['licence-type']
        );

        $response = $this->getServiceLocator()->get('Entity\Application')
            ->createNew($organisationId, $newApplicationData);

        $applicationId = $response['application'];

        $this->getServiceLocator()->get('Entity\ApplicationCompletion')
            ->updateCompletionStatuses($applicationId, 'type_of_licence');

        return $applicationId;
    }

    protected function cancelFees($applicationId)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        $this->getServiceLocator()->get('Processing\Application')->cancelFees($licenceId);
    }
}
