<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Task implements BusinessRuleInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function validate($data)
    {
        if (!isset($data['actionDate'])) {
            $data['actionDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s');
        }

        return $data;
    }
}
