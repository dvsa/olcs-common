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

    public function validate($data, $description)
    {
        $now = $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s');

        $validated = [];

        $validated['amount']         = $data['fee-details']['amount'];
        $validated['invoicedDate']   = $data['fee-details']['createdDate'];
        $validated['feeType']        = $data['fee-details']['feeType'];
        $validated['description']    = $description;
        $validated['feeStatus']      = FeeEntityService::STATUS_OUTSTANDING;
        $validated['createdBy']      = $data['user'];
        $validated['lastModifiedBy'] = $data['user'];
        $validated['createdOn']      = $now;
        $validated['lastModifiedOn'] = $now;

        return $validated;
    }
}
