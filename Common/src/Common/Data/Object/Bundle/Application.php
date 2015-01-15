<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Application
 * @package Common\Data\Object\Bundle
 */
class Application extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function init(ServiceLocatorInterface $serviceLocator)
    {
        $this->addChild('licence');
    }
}
