<?php

/**
 * Abstract Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Common\Controller\Lva\Interfaces\AdapterInterface;

/**
 * Abstract Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAdapter implements ServiceLocatorAwareInterface, AdapterInterface
{
    use ServiceLocatorAwareTrait;

    protected $lva;
    protected $applicationAdapter;

    /**
     * Get an instance of application lva adapter
     *
     * @return \Common\Controller\Lva\Adapters\ApplicationLvaAdapter
     */
    protected function getApplicationAdapter()
    {
        if ($this->applicationAdapter === null) {
            $this->applicationAdapter = $this->getLvaAdapter('Application');
        }

        return $this->applicationAdapter;
    }

    /**
     * Get an instance of an Lva Adapter
     *
     * @param string $lva
     * @return AbstractLvaAdapter
     */
    protected function getLvaAdapter($lva = null)
    {
        if ($lva === null) {
            $lva = $this->lva;
        }

        return $this->getServiceLocator()->get($lva . 'LvaAdapter');
    }
}
