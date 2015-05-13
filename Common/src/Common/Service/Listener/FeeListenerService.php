<?php

/**
 * Fee Listener Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Listener;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Fee Listener Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeListenerService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const EVENT_WAIVE = 'Waive';
    const EVENT_PAY = 'Pay';

    public function trigger($id, $eventType)
    {
        $method = 'trigger' . $eventType;
        if (method_exists($this, $method)) {
            return $this->$method($id);
        }

        throw new Exception('Event type not found');
    }

    protected function triggerPay($id)
    {
        $this->maybeProcessApplicationFee($id);
        $this->maybeContinueLicence($id);
    }

    protected function triggerWaive($id)
    {
        $this->maybeProcessApplicationFee($id);
        $this->maybeContinueLicence($id);
    }

    /**
     * If the fee type is a continuation fee then check whether to continue licence
     *
     * @param int $feeId Fee ID
     *
     * @return bool Whether the licence was continued
     */
    protected function maybeContinueLicence($feeId)
    {
        $feeService = $this->getServiceLocator()->get('Entity\Fee');
        $fee = $feeService->getOverview($feeId);

        // Fee type is continuation fee
        if ($fee['feeType']['feeType']['id'] !== \Common\Service\Data\FeeTypeDataService::FEE_TYPE_CONT) {
            return false;
        }

        // there is an ongoing continuation associated to a particular licence and the status is 'Checklist accepted'
        $continuationDetailService = $this->getServiceLocator()->get('Entity\ContinuationDetail');
        $continuationDetail = $continuationDetailService->getOngoingForLicence($fee['licenceId']);
        if ($continuationDetail === false) {
            return false;
        }

        // the licence status is Valid, Curtailed or Suspended
        $validLicenceStatuses = [
            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_VALID,
            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_CURTAILED,
            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_SUSPENDED,
        ];
        if (!in_array($continuationDetail['licence']['status']['id'], $validLicenceStatuses)) {
            return false;
        }

        // there are no other outstanding or (waive recommended) continuation fees associated to the licence
        $outstandingFees = $feeService->getOutstandingContinuationFee($fee['licenceId']);
        if ($outstandingFees['Count'] !== 0) {
            return false;
        }

        // @todo Continue the Licence story OLCS-7310

        // add success message
        // @note not ideal to be using the FlashMessenger from a service, but in this circumstance it would be
        // difficult to get the return status all the way to the controller
        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage('licence.continued.message');

        return true;
    }

    /**
     * @NOTE When waiving or paying an application fee, we need to check if there are any outstanding fees, if not then
     *  we can make the application valid
     *
     * @param int $id
     */
    protected function maybeProcessApplicationFee($id)
    {
        $feeService = $this->getServiceLocator()->get('Entity\Fee');

        $application = $feeService->getApplication($id);

        if ($application === null
            || $application['isVariation']
            || $application['status']['id'] !== ApplicationEntityService::APPLICATION_STATUS_GRANTED
            // @todo - check this, I think 'status' should actually be 'feeStatus'?
        ) {
            return;
        }

        // if there are any outstanding GRANT fees then don't continue
        $outstandingGrantFees = $feeService->getOutstandingGrantFeesForApplication($application['id']);
        if (!empty($outstandingGrantFees)) {
            return;
        }

        $this->getServiceLocator()->get('Processing\Application')->validateApplication($application['id']);
    }
}
