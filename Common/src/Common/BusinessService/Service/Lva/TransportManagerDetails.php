<?php

/**
 * Transport Manager Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\Service\Entity\TransportManagerApplicationEntityService;
use Common\Service\Entity\ContactDetailsEntityService;

/**
 * Transport Manager Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerDetails implements BusinessServiceInterface, BusinessServiceAwareInterface
{
    use BusinessServiceAwareTrait;

    /**
     * Format and save the TransportManager/Details section
     *
     * @param array $params
     * @return ResponseInterface
     */
    public function process(array $params)
    {
        $responseData = [];

        $contactDetailsResponse = $this->persistHomeContactDetails($params);

        if (!$contactDetailsResponse->isOk()) {
            return $contactDetailsResponse;
        }

        $responseData['contactDetailsId'] = $contactDetailsResponse->getData()['id'];

        $personResponse = $this->persistPerson($params);

        if (!$personResponse->isOk()) {
            return $personResponse;
        }

        $responseData['personId'] = $personResponse->getData()['id'];

        if (isset($params['workContactDetails']['id'])) {
            $updatingWorkAddress = true;
        } else {
            $updatingWorkAddress = false;
        }

        $workContactDetailsResponse = $this->persistWorkContactDetails($params);

        if (!$workContactDetailsResponse->isOk()) {
            return $workContactDetailsResponse;
        }

        $responseData['workContactDetailsId'] = $workContactDetailsResponse->getData()['id'];

        // If we have created the work address, then we need to update the TM record
        if (!$updatingWorkAddress) {
            $tmResponse = $this->persistTransportManager($params, $responseData['workContactDetailsId']);

            if (!$tmResponse->isOk()) {
                return $tmResponse;
            }

            $responseData['transportManagerId'] = $tmResponse->getData()['id'];
        }

        $tmaResponse = $this->persistTransportManagerApplication($params);

        if (!$tmaResponse->isOk()) {
            return $tmaResponse;
        }

        $responseData['transportManagerApplicationId'] = $tmaResponse->getData()['id'];

        return new Response(Response::TYPE_SUCCESS, $responseData);
    }

    /**
     * Persist email address and home address
     *
     * @param array $params
     * @return Response
     */
    protected function persistHomeContactDetails($params)
    {
        $contactDetails = [
            'data' => [
                'id' => $params['contactDetails']['id'],
                'version' => $params['contactDetails']['version'],
                'emailAddress' => $params['data']['details']['emailAddress'],
                'addresses' => [
                    'address' => $params['data']['homeAddress']
                ]
            ]
        ];

        return $this->getBusinessServiceManager()->get('Lva\ContactDetails')->process($contactDetails);
    }

    /**
     * Persist work address
     *
     * @param array $params
     * @return Response
     */
    protected function persistWorkContactDetails($params)
    {
        $workContactDetails = [
            'data' => [
                'addresses' => [
                    'address' => $params['data']['workAddress']
                ]
            ]
        ];

        if (isset($params['workContactDetails']['id'])) {
            $workContactDetails['data']['id'] = $params['workContactDetails']['id'];
            $workContactDetails['data']['version'] = $params['workContactDetails']['version'];
        } else {
            $workContactDetails['data']['contactType'] = ContactDetailsEntityService::CONTACT_TYPE_TRANSPORT_MANAGER;
        }

        return $this->getBusinessServiceManager()->get('Lva\ContactDetails')->process($workContactDetails);
    }

    /**
     * Persist person's birth place
     *
     * @param array $params
     * @return Response
     */
    protected function persistPerson($params)
    {
        $personParams = [
            'data' => [
                'id' => $params['person']['id'],
                'version' => $params['person']['version'],
                'birthPlace' => $params['data']['details']['birthPlace'],
            ]
        ];

        return $this->getBusinessServiceManager()->get('Lva\Person')->process($personParams);
    }

    /**
     * Persist transport managers work address
     *
     * @param array $params
     * @param int $workContactDetailsId
     * @return Response
     */
    protected function persistTransportManager($params, $workContactDetailsId)
    {
        $transportManagerParams = [
            'data' => [
                'id' => $params['transportManager']['id'],
                'version' => $params['transportManager']['version'],
                'workCd' => $workContactDetailsId
            ]
        ];

        return $this->getBusinessServiceManager()->get('Lva\TransportManager')->process($transportManagerParams);
    }

    /**
     * Update transport manager application status
     *
     * @param array $params
     * @return Response
     */
    protected function persistTransportManagerApplication($params)
    {
        $responsibilities = $params['data']['responsibilities'];
        $hours = $responsibilities['hoursOfWeek']['hoursPerWeekContent'];

        $tmApplicationParams = [
            'data' => [
                'id' => $params['transportManagerApplication']['id'],
                'version' => $params['transportManagerApplication']['version'],
                'tmType' => $responsibilities['tmType'],
                'additionalInformation' => $responsibilities['additionalInformation'],
                'hoursMon' => $hours['hoursMon'],
                'hoursTue' => $hours['hoursTue'],
                'hoursWed' => $hours['hoursWed'],
                'hoursThu' => $hours['hoursThu'],
                'hoursFri' => $hours['hoursFri'],
                'hoursSat' => $hours['hoursSat'],
                'hoursSun' => $hours['hoursSun'],
                'isOwner' => $responsibilities['isOwner'],
                'operatingCentres' => $responsibilities['operatingCentres']
            ]
        ];

        if ($params['submit']) {
            $submitStatus = TransportManagerApplicationEntityService::STATUS_AWAITING_SIGNATURE;
            $tmApplicationParams['data']['tmApplicationStatus'] = $submitStatus;
        }

        return $this->getBusinessServiceManager()->get('Lva\TransportManagerApplication')
            ->process($tmApplicationParams);
    }
}
