<?php

/**
 * Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Trading Names
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TradingNames implements BusinessServiceInterface, BusinessRuleAwareInterface, ServiceLocatorAwareInterface
{
    use BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $orgId = $params['orgId'];
        $licenceId = $params['licenceId'];
        $tradingNames = $params['tradingNames'];

        $tradingNamesRule = $this->getBusinessRuleManager()->get('TradingNames');

        $filtered = $tradingNamesRule->filter($tradingNames);

        $hasChanged = $this->hasChangedTradingNames($orgId, $filtered);

        $tradingNamesData = $tradingNamesRule->validate($filtered, $orgId, $licenceId);

        $this->getServiceLocator()->get('Entity\TradingNames')->save($tradingNamesData);

        $response = new Response();
        $response->setType(Response::TYPE_PERSIST_SUCCESS);
        $response->setData(['hasChanged' => $hasChanged]);

        return $response;
    }

    protected function hasChangedTradingNames($orgId, $tradingNames)
    {
        return $this->getServiceLocator()->get('Entity\Organisation')->hasChangedTradingNames($orgId, $tradingNames);
    }
}
