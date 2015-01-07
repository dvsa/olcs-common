<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Licence
 * @package Common\Data\Object\Bundle
 */
class TransportManager extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function init(ServiceLocatorInterface $serviceLocator)
    {
        $contactDetails = new Bundle();
        $contactDetails->addChild('person');
        $contactDetails->addChild('address');

        $this->addChild('contactDetails', $contactDetails);
    }
}
