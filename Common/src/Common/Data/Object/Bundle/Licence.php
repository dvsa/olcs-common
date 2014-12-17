<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\Di\ServiceLocatorInterface;

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
    public function init(ServiceLocatorInterface $serviceLocator)
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
             ->addChild('goodsOrPSv')
             ->addChild('licenceType')
             ->addChild('trafficeArea')
             ->addChild('organisation', $organisation);
    }
}