<?php

namespace Common\Service;

use Laminas\Navigation\Service\ConstructedNavigationFactory;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class NavigationFactory
 * @package Olcs\Service
 */
class NavigationFactory extends ConstructedNavigationFactory implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @param null $nav
     */
    public function __construct($nav = null)
    {
        $this->config = $nav;
    }

    /**
     * @param $nav
     * @return \Laminas\Navigation\Navigation
     */
    public function getNavigation($nav)
    {
        $this->config = $nav;
        return $this->createService($this->getServiceLocator());
    }
}
