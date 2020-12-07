<?php

namespace Common\Service\Listener;

use Common\Exception\BailOutException;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateInterface;
use Laminas\EventManager\ListenerAggregateTrait;
use Laminas\Mvc\MvcEvent;

/**
 * Bail Out Listener
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BailOutListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events)
    {
        $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError']);
    }

    public function onDispatchError(MvcEvent $e)
    {
        $exception = $e->getParam('exception');

        if (!$exception instanceof BailOutException) {
            return;
        }

        return $e->setResult($exception->getResponse());
    }
}
