<?php

namespace Common\Data\Object\Bundle;

use Common\Data\Object\Bundle;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class EbsrSubmission
 * @package Common\Data\Object\Bundle
 */
class EbsrSubmission extends Bundle
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    protected function doInit(ServiceLocatorInterface $serviceLocator)
    {
        $this->addChild('ebsrSubmissionStatus');
        $this->addChild('ebsrSubmissionType');

        $licence = new Bundle();
        $licence->addChild('organisation');

        $busReg = new Bundle();
        $busReg->addChild('status');
        $busReg->addChild('otherServices');
        $busReg->addChild('licence', $licence);

        $this->addChild('busReg', $busReg);
    }
}
