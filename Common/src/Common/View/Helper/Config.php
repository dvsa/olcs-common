<?php

namespace Common\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;

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
        /** @var  \Zend\View\HelperPluginManager $sm */
        $sm = $this->getServiceLocator();

        return (array) $sm->getServiceLocator()->get('Config');
    }
}
