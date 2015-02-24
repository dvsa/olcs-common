<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Licence
 * @package Common\Data\Object\Bundle
 */
class Licence extends Bundle
{
    /**
     * @TODO over time move these child bundles into separate classes and pull in via SL
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $appeals = new Bundle();
        $appeals->addChild('outcome')
                ->addChild('reason');

        $stays = new Bundle();
        $stays->addChild('stayType')
              ->addChild('outcome');

        $cases = new Bundle();
        $cases->addChild('appeals', $appeals)
              ->addChild('stays', $stays);

        $organisation = new Bundle();
        $organisation->addChild('organisationPersons')
                     ->addChild('tradingNames');

        $this->addChild('cases', $cases)
             ->addChild('status')
             ->addChild('goodsOrPsv')
             ->addChild('licenceType')
             ->addChild('trafficArea')
             ->addChild('organisation', $organisation);
    }
}
