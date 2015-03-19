<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * EventHistory bundle
 * @package Common\Data\Object\Bundle
 */
class EventHistory extends Bundle
{
    /**
     * @TODO over time move these child bundles into separate classes and pull in via SL
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {

        $person = new Bundle('person');

        $contactDetails = new Bundle('contactDetails');
        $contactDetails->addChild('person', $person);

        $user = new Bundle('user');
        $user->addChild('contactDetails', $contactDetails);


        $this->addChild('user', $user);

        $this->addChild('eventHistoryType');
    }
}
