<?php

/**
 * Abstract Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Helper Service
 *
 * @todo This is essentially a dumping ground from abstractHelperController we may want to re-factor
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractHelperService implements HelperServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    private $serviceFactory;

    /**
     *
     * @param \Common\Service\Helper\HelperServiceFactory $factory
     */
    public function setHelperServiceFactory(HelperServiceFactory $factory)
    {
        $this->serviceFactory = $factory;
    }

    /**
     * Get another helper service
     *
     * @param string $name
     * @return \Common\Service\Helper\HelperServiceInterface
     */
    protected function getHelperService($name)
    {
        return $this->serviceFactory->getHelperService($name);
    }
}
