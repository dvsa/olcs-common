<?php

/**
 * Application Type Of Licence Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\TypeOfLicenceAdapterInterface;
use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Common\Controller\Lva\Traits\ControllerAwareTrait;
use Common\Service\Entity\LicenceEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Entity\ApplicationCompletionEntityService;

/**
 * Application Type Of Licence Adapter
 * @NOTE This is a CONTROLLER adapter and thus contains logic similar to that of a controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceAdapter
implements TypeOfLicenceAdapterInterface, ServiceLocatorAwareInterface, ControllerAwareInterface
{
    use ServiceLocatorAwareTrait,
        ControllerAwareTrait;

    protected $queryParams = [];

    public function getQueryParams()
    {
        return ['query' => $this->queryParams];
    }

    public function getRouteParams()
    {
        return ['action' => 'confirmation'];
    }

    public function doesChangeRequireConfirmation(array $postData, array $currentData)
    {
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

    public function createFee($applicationId)
    {
        $licenceId = $this->getServiceLocator()->get('Entity\Application')
            ->getLicenceIdForApplication($applicationId);

        $this->getServiceLocator()->get('Processing\Application')
            ->createFee($applicationId, $licenceId, FeeTypeDataService::FEE_TYPE_APP);
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
                    ->addWarningMessage('No changes to the type of licence detected');

                return $this->getController()->redirect()->toRoute(null, ['action' => null], [], true);
            }

            $organisation = $applicationService->getOrganisation($applicationId);

            $this->removeApplication($applicationId);

            $newApplicationId = $this->createApplication($organisation['id'], $query);

            $this->createFee($newApplicationId);

            return $this->getController()->redirect()->toRoute('lva-application', ['application' => $newApplicationId]);
        }

        return $this->getServiceLocator()->get('Helper\Form')->createForm('GenericConfirmation');
    }

    protected function removeApplication($applicationId)
    {
        if ($applicationId !== null) {
            $this->getServiceLocator()->get('Entity\Task')->deleteList(['application' => $applicationId]);
        }

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
