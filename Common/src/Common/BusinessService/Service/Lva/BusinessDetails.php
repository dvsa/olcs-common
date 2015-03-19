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
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails implements
    BusinessServiceInterface,
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessRuleAwareTrait,
        ServiceLocatorAwareTrait,
        BusinessServiceAwareTrait;

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

        $data = $params['data'];
        $registeredAddress = $data['registeredAddress'];
        $natureOfBusinesses = $data['data']['natureOfBusinesses'];

        $isDirty = false;

        if (!empty($params['tradingNames'])) {

            $tradingNameParams = [
                'orgId' => $orgId,
                'licenceId' => $licenceId,
                'tradingNames' => $tradingNames
            ];

            $response = $this->getBusinessServiceManager()->get('Lva\TradingNames')->process($tradingNameParams);

            // If there was a failure in the sub-process forward the response straight away
            if ($response->getType() !== Response::TYPE_PERSIST_SUCCESS) {
                return $response;
            }

            $isDirty = $response->getData()['hasChanged'];
        }

        $contactDetailsId = null;

        if (isset($registeredAddress)) {
            $registeredAddressParams = [
                'orgId' => $orgId,
                'registeredAddress' => $registeredAddress
            ];

            $response = $this->getBusinessServiceManager()->get('Lva\RegisteredAddress')
                ->process($registeredAddressParams);

            // If there was a failure in the sub-process forward the response straight away
            if ($response->getType() !== Response::TYPE_PERSIST_SUCCESS) {
                return $response;
            }

            $isDirty = $isDirty ?: $response->getData()['hasChanged'];

            $addressResponseData = $response->getData();

            if (isset($addressResponseData['contactDetailsId'])) {
                $contactDetailsId = $addressResponseData['contactDetailsId'];
            }
        }

        // We need to check if we have changed nature of businesses before we save
        $isDirty = $isDirty ?: $this->hasChangedNatureOfBusiness($orgId, $natureOfBusinesses);

        $validatedData = $this->getBusinessRuleManager()->get('BusinessDetails')
            ->validate($orgId, $data, $natureOfBusinesses, $contactDetailsId);

        $this->getServiceLocator()->get('Entity\Organisation')->save($validatedData);

        if ($isDirty) {
            $response = $this->getBusinessServiceManager()->get('Lva\BusinessDetailsChangeTask')
                ->process(['licenceId' => $licenceId]);

            if (!in_array($response->getType(), [Response::TYPE_PERSIST_SUCCESS, Response::TYPE_NO_OP])) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_PERSIST_SUCCESS);
        $response->setData([]);

        return $response;
    }

    protected function hasChangedNatureOfBusiness($orgId, $natureOfBusiness)
    {
        return $this->getServiceLocator()->get('Entity\Organisation')
            ->hasChangedNatureOfBusiness($orgId, $natureOfBusiness);
    }
}
