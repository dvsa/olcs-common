<?php

/**
 * Environmental Complaint
 */
namespace Common\BusinessService\Service\Cases\Complaint;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Environmental Complaint
 */
class EnvironmentalComplaint implements
    BusinessServiceInterface,
    BusinessRuleAwareInterface,
    BusinessServiceAwareInterface,
    ServiceLocatorAwareInterface
{
    use BusinessRuleAwareTrait;
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
        // validate data
        $data = $this->getBusinessRuleManager()->get('EnvironmentalComplaint')->validate($params['data']);

        // save Complainant
        $contactDetailsId = $this->saveComplainant($data, $params['address']);
        $data['complainantContactDetails'] = $contactDetailsId;

        // save Complaint
        $result = $this->getServiceLocator()->get('Entity\Complaint')->save($data);

        // save related operating centres to ocComplaint table
        $complaintId = isset($result['id']) ? $result['id'] : $data['id'];
        $this->saveOcComplaints($complaintId, $data['ocComplaints']);

        if (empty($params['id'])) {
            // create a task
            $response = $this->getBusinessServiceManager()->get('Cases\Complaint\EnvironmentalComplaintTask')
                ->process(
                    [
                        'caseId' => $params['caseId'],
                    ]
                );

            if (!$response->isOk()) {
                return $response;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        return $response;
    }

    /**
     * Saves the person, contact details and address entities, if required based on data.
     * Prevent the person id from ever being overwritten by inserting a new record if the complainant name changes
     * or keep existing if unchanged.
     *
     * @param array $data
     * @param array $address
     * @return mixed
     */
    private function saveComplainant($data, $address)
    {
        $personService = $this->getServiceLocator()
            ->get('DataServiceManager')
            ->get('Generic\Service\Data\Person');

        if (!empty($data['id'])) {
            // get the current person id
            $existing = $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
                'Complaint',
                'GET',
                $data['id'],
                [
                    'children' => [
                        'complainantContactDetails' => [
                            'children' => [
                                'address',
                                'person' => [
                                    'forename',
                                    'familyName'
                                ]
                            ]
                        ]
                    ]
                ]
            );

            // save the address
            $addressSaved = $this->getServiceLocator()->get('Entity\Address')->save($address);
            $addressId = isset($addressSaved['id']) ? $addressSaved['id'] :
                $existing['complainantContactDetails']['address']['id'];

            $contactDetailsToSave = [
                'id' => $existing['complainantContactDetails']['id'],
                'address' => $addressId,
            ];

            // we may not need to modify the person details at all
            $person = $existing['complainantContactDetails']['person'];

            if ($data['complainantForename'] != $person['forename']
                || $data['complainantFamilyName'] != $person['familyName']) {
                $person['forename'] = $data['complainantForename'];
                $person['familyName'] = $data['complainantFamilyName'];

                $personId = $personService->save($person);
                $contactDetailsToSave = array_merge(
                    $contactDetailsToSave,
                    [
                        'version' => $existing['complainantContactDetails']['version'],
                        'person' => $personId
                    ]
                );
            }
        } else {
            $person['forename'] = $data['complainantForename'];
            $person['familyName'] = $data['complainantFamilyName'];
            $personId = $personService->save($person);

            $addressSaved = $this->getServiceLocator()->get('Entity\Address')->save($address);
            $addressId = isset($addressSaved['id']) ? $addressSaved['id'] : $address['id'];

            $contactDetailsToSave = [
                'person' => $personId,
                'address' => $addressId,
                'contactType' => 'ct_complainant'
            ];
        }

        if (!empty($contactDetailsToSave)) {
            $contactDetailsService = $this->getServiceLocator()
                ->get('DataServiceManager')
                ->get('Generic\Service\Data\ContactDetails');

            $result = $contactDetailsService->save($contactDetailsToSave);
        }

        return isset($result) ? $result : $contactDetailsToSave['id'];
    }

    /**
     * Saves Operating Centres
     *
     * @param int $complaintId
     * @param array $operatingCentres
     * @return bool
     */
    private function saveOcComplaints($complaintId, $operatingCentres)
    {
        if (empty($complaintId)) {
            return false;
        }

        // clear any existing
        $this->getServiceLocator()->get('Helper\Rest')
            ->makeRestCall('OcComplaint', 'DELETE', ['complaint' => $complaintId]);

        if (is_array($operatingCentres)) {
            foreach ($operatingCentres as $operatingCentreId) {
                $this->getServiceLocator()->get('Helper\Rest')->makeRestCall(
                    'OcComplaint',
                    'POST',
                    [
                        'complaint' => $complaintId,
                        'operatingCentre' => $operatingCentreId
                    ]
                );
            }
        }

        return true;
    }
}
