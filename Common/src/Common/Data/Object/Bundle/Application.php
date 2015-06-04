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
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $this->addChild('licence');

        $address = new Bundle();
        $address->addChild('address');

        $contactDetails = new Bundle();
        $contactDetails->addChild('person');

        $homeCd = new Bundle();
        $homeCd->addChild('homeCd', $contactDetails);

        $transportManager = new Bundle();
        $transportManager->addChild('transportManager', $homeCd);

        $operatingCentres = new Bundle();
        $operatingCentres->addChild('operatingCentre', $address);

        $this->addChild('operatingCentres', $operatingCentres);
        $this->addChild('goodsOrPsv');
        $this->addChild('status');
        $this->addChild('transportManagers', $transportManager);
    }
}
