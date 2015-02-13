<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class BusReg
 * @package Common\Data\Object\Bundle
 */
class BusReg extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function init(ServiceLocatorInterface $serviceLocator)
    {
        $this->addChild('licence', $serviceLocator->get('Licence'));
        $this->addChild('status');
        $this->addChild('revertStatus');
        $this->addChild('withdrawnReason');
        $this->addChild('parent');
        $this->addChild('busNoticePeriod');
    }

    /**
     * Gets the default bundle name
     *
     * @return string
     */
    public function getDefaultBundle()
    {
        return 'BusReg';
    }
}
