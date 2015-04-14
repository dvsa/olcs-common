<?php

/**
 * Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Service\Entity\FeeEntityService;

/**
 * Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Fee implements BusinessRuleInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function validate($formData, $description, $now)
    {
        $data = [];

        $data['amount']         = $formData['fee-details']['amount'];
        $data['invoicedDate']   = $formData['fee-details']['createdDate'];
        $data['feeType']        = $formData['fee-details']['feeType'];
        $data['description']    = $description;
        $data['feeStatus']      = FeeEntityService::STATUS_OUTSTANDING;
        $data['createdBy']      = $formData['createdBy'];
        $data['lastModifiedBy'] = $formData['lastModifiedBy'];
        $data['createdOn']      = $now;
        $data['lastModifiedOn'] = $now;

        return $data;
    }
}
