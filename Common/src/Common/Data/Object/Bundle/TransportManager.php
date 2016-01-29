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
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $homeCd = new Bundle();
        $homeCd->addChild('person');
        $homeCd->addChild('address');

        $workCd = new Bundle();
        $workCd->addChild('address');

        $this->addChild('homeCd', $homeCd);
        $this->addChild('workCd', $workCd);

        $this->addChild('tmStatus');
        $this->addChild('tmType');
    }
}
