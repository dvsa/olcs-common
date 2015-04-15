<?php

/**
 * Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\BusinessService\Service;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\Response;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Fee implements
    BusinessServiceInterface,
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait,
        BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Processes and persists fee data
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $description = $this->getServiceLocator()->get('Entity\FeeType')
            ->getById($params['fee-details']['feeType'])['description'];

        $data = $this->getBusinessRuleManager()->get('Fee')
            ->validate($params, $description);

        $saved = $this->getServiceLocator()->get('Entity\Fee')->save($data);

        return new Response(Response::TYPE_SUCCESS, ['id' => $saved['id']]);
    }
}
