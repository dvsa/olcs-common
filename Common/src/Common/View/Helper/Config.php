<?php

namespace Common\View\Helper;

use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Helper\AbstractHelper;

/**
 * Class return Config to view
 */
class Config extends AbstractHelper implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Return config
     *
     * @return array
     */
    public function __invoke()
    {
        /** @var  \Laminas\View\HelperPluginManager $sm */
        $sm = $this->getServiceLocator();

        return (array) $sm->getServiceLocator()->get('Config');
    }
}
