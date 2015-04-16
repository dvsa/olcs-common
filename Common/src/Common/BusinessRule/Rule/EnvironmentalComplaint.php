<?php

/**
 * Environmental Complaint
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Common\Service\Entity\ComplaintEntityService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Environmental Complaint
 */
class EnvironmentalComplaint implements BusinessRuleInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function validate($data)
    {
        $data['isCompliance'] = false;

        $data['closedDate']
            = ($data['status'] == ComplaintEntityService::COMPLAIN_STATUS_CLOSED)
                ? $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s') : null;

        return $data;
    }
}
