<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConditionUndertaking
 * @package Common\Data\Object\Bundle
 */
class ConditionUndertaking extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $address = new Bundle();
        $address->addChild('address');

        $this->addChild('conditionType');
        $this->addChild('operatingCentre', $address);
    }
}
