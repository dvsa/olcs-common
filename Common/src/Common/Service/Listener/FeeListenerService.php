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

    public function trigger($id, $eventType)
    {
        $method = 'trigger' . $eventType;
        if (method_exists($this, $method)) {
            return $this->$method($id);
        }

        throw new Exception('Event type not found');
    }

    /**
     * @NOTE When waiving or paying an application fee, we need to check if there are any outstanding fees, if not then
     *  we can make the application valid
     *
     * @param int $id
     */
    protected function triggerWaive($id)
    {
        $feeService = $this->getServiceLocator()->get('Entity\Fee');

        $application = $feeService->getApplication($id);

        if ($application === null
            || $application['isVariation']
            || $application['status']['id'] !== ApplicationEntityService::APPLICATION_STATUS_GRANTED
        ) {
            return;
        }

        $fees = $feeService->getOutstandingFeesForApplication($application['id']);

        if (!empty($fees)) {
            return;
        }

        $this->getServiceLocator()->get('Processing\Application')->validateApplication($application['id']);
    }
}
