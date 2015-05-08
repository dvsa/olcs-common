<?php

/**
 * IRFO Details
 */
namespace Common\BusinessService\Service\Operator;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * IRFO Details
 */
class IrfoDetails implements
    BusinessServiceInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessServiceAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        // save IRFO Contact Details
        $contactDetails = array_merge(
            // existing data
            !empty($params['data']['irfoContactDetails']) ? $params['data']['irfoContactDetails'] : [],
            // form data
            [
                // set type to IRFO secific
                'contactType' => 'ct_irfo_op',
                // new address
                'address' => $params['address'],
                // new email
                'emailAddress' => $params['contact']['email'],
                // remove phone contacts, they will be saved separately due to form fields format
                'phoneContacts' => null
            ]
        );

        $contactResponse = $this->getBusinessServiceManager()
            ->get('Lva\ContactDetails')
            ->process(
                [
                    'data' => $contactDetails
                ]
            );

        if (!$contactResponse->isOk()) {
            return $contactResponse;
        }

        // get Id of the saved IRFO Contact Details
        $irfoContactDetailsId = $contactResponse->getData()['id'];

        // save Phone Contact
        $phoneResponse = $this->getBusinessServiceManager()
            ->get('Lva\PhoneContact')
            ->process(
                [
                    'data' => [
                        'contact' => $params['contact']
                    ],
                    'correspondenceId' => $irfoContactDetailsId
                ]
            );

        if (!$phoneResponse->isOk()) {
            return $phoneResponse;
        }

        // link Organisation with the IRFO Contact Details
        $data = array_merge(
            // existing data
            $params['data'],
            [
                // new IRFO Contact Details
                'irfoContactDetails' => $irfoContactDetailsId,
                // remove trading names, they will be saved separately due to form fields format
                'tradingNames' => null
            ]
        );

        // save IRFO Details
        $this->getServiceLocator()->get('Entity\Organisation')->save($data);

        // save Trading Names
        if (!empty($params['data']['tradingNames'])) {
            $response = $this->getBusinessServiceManager()->get('Lva\TradingNames')->process(
                [
                    'orgId' => $params['id'],
                    'tradingNames' => array_column($params['data']['tradingNames'], 'name')
                ]
            );

            // If there was a failure in the sub-process forward the response straight away
            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);

        return $response;
    }
}
