<?php

/**
 * Get Placeholder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Get Placeholder
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GetPlaceholderFactory extends AbstractHelper implements FactoryInterface
{
    private $placeholder;

    private $containers = [];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->placeholder = $serviceLocator->get('placeholder');

        return $this;
    }

    public function __invoke($name)
    {
        $placeholder = $this->placeholder;

        if (!isset($this->containers[$name])) {
            $this->containers[$name] = new GetPlaceholder($placeholder($name));
        }

        return $this->containers[$name];
    }
}
