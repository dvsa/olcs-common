<?php

/**
 * Fee Listener Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Listener;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Data\FeeTypeDataService;

/**
 * Fee Listener Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
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
        $this->maybeProcessGrantingFee($id);
    }

    protected function triggerWaive($id)
    {
        $this->maybeProcessGrantingFee($id);
    }

    /**
     * If the fee type is a interim, then check if we do need in-force processing
     *
     * @param int $feeId Fee ID
     *
     * @return bool Whether the licence was continued
     */
    protected function maybeProcessGrantingFee($feeId)
    {
        $fee = $this->getServiceLocator()->get('Entity\Fee')->getFeeDetailsForInterim($feeId);

        if ($fee['feeType']['feeType']['id'] !== FeeTypeDataService::FEE_TYPE_GRANTINT) {
            return false;
        }

        if (!isset($fee['application']['interimStatus']['id']) ||
            $fee['application']['interimStatus']['id'] !== ApplicationEntityService::INTERIM_STATUS_GRANTED) {
            return false;
        }

        $this->getServiceLocator()->get('Helper\Interim')->grantInterim($fee['application']['id']);
    }
}
