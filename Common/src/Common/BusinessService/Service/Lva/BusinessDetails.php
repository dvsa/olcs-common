<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails implements BusinessServiceInterface, BusinessRuleAwareInterface, BusinessServiceAwareInterface
{
    use BusinessRuleAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $isDirty = false;

        if (isset($params['tradingNames']['trading_name']) && !empty($params['tradingNames']['trading_name'])) {

            $response = $this->getBusinessServiceManager()->get('Lva\TradingNames')->process($params);

            // If there was a failure in the sub-process forward the response straight away
            if ($response->getType() !== Response::TYPE_PERSIST_SUCCESS) {
                return $response;
            }

            $isDirty = $response->getData()['hasChanged'];
        }
    }
}
