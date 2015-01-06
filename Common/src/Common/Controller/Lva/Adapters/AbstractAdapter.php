<?php

/**
 * Abstract Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Abstract Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractAdapter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $applicationAdapter;
    protected $licenceAdapter;
    protected $variationAdapter;

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
     * Get an instance of licence lva adapter
     *
     * @return \Common\Controller\Lva\Adapters\LicenceLvaAdapter
     */
    protected function getLicenceAdapter()
    {
        if ($this->licenceAdapter === null) {
            $this->licenceAdapter = $this->getLvaAdapter('Licence');
        }

        return $this->licenceAdapter;
    }

    /**
     * Get an instance of variation lva adapter
     *
     * @return \Common\Controller\Lva\Adapters\VariationLvaAdapter
     */
    protected function getVariationAdapter()
    {
        if ($this->variationAdapter === null) {
            $this->variationAdapter = $this->getLvaAdapter('Variation');
        }

        return $this->variationAdapter;
    }

    /**
     * Get an instance of an Lva Adapter
     *
     * @param string $lva
     * @return AbstractLvaAdapter
     */
    protected function getLvaAdapter($lva)
    {
        return $this->getServiceLocator()->get($lva . 'LvaAdapter');
    }
}
