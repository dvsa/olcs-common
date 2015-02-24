<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Publication
 * @package Common\Data\Object\Bundle
 */
class Publication extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $this->addChild('publicationLinks');
        $this->addChild('trafficArea');
    }

    /**
     * Gets the default bundle name
     *
     * @return string
     */
    public function getDefaultBundle()
    {
        return 'Publication';
    }
}
