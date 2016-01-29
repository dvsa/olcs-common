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
        // @todo Remove all references to this as it is no longer used
    }
}
