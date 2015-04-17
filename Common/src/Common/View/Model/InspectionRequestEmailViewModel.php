<?php

/**
 * Inspect Request Email View Model
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\View\Model;

use Zend\View\Model\ViewModel;

/**
 * Inspect Request Email View Model
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InspectionRequestEmailViewModel extends ViewModel
{
    protected $terminate = true;
    protected $template = 'email/inspection-request';

    /**
     * Populate the view from entity data
     *
     * @param array $inspectionRequest
     * @param array $user
     * @param array $peopleData
     * @param array $workshop
     * @param \Zend\I18n\Translator\TranslatorInterface $translator
     * @return this
     */
    public function populate($inspectionRequest, $user, $peopleData, $workshop, $translator)
    {
        $people = [];
        if (isset($peopleData['Results']) && !empty($peopleData['Results'])) {
            $people =  array_map(
                function ($peopleResult) {
                    return $peopleResult['person'];
                },
                $peopleData['Results']
            );
        }

        $tradingNames = [];
        if (!empty($inspectionRequest['licence']['organisation']['tradingNames'])) {
            $tradingNames = array_map(
                function ($tradingName) {
                    return $tradingName['name'];
                },
                $inspectionRequest['licence']['organisation']['tradingNames']
            );
        }

        $requestDate = new \DateTime($inspectionRequest['requestDate']);
        $dueDate =  new \DateTime($inspectionRequest['dueDate']);
        $expiryDate = new \DateTime($inspectionRequest['licence']['expiryDate']);

        $data = [
            'inspectionRequestId' => $inspectionRequest['id'],
            'currentUserName' => $user['loginId'],
            'currentUserEmail' => $user['emailAddress'],
            'inspectionRequestDateRequested' => $requestDate->format('d/m/Y H:i:s'),
            'inspectionRequestNotes' => $inspectionRequest['requestorNotes'],
            'inspectionRequestDueDate' => $dueDate->format('d/m/Y H:i:s'),
            'ocAddress' => $inspectionRequest['operatingCentre']['address'],
            'inspectionRequestType' => $inspectionRequest['requestType']['description'],
            'licenceNumber' => $inspectionRequest['licence']['licNo'],
            'licenceType' => $translator->translate($inspectionRequest['licence']['licenceType']['id']),
            'totAuthVehicles' => $this->getTotAuthVehicles($inspectionRequest),
            'totAuthTrailers' => $this->getTotAuthTrailers($inspectionRequest),
            'numberOfOperatingCentres' => 2, // @TODO!
            'expiryDate' => $expiryDate->format('d/m/Y'),
            'operatorId' => $inspectionRequest['licence']['organisation']['id'],
            'operatorName' => $inspectionRequest['licence']['organisation']['name'],
            'operatorEmail' => $inspectionRequest['licence']['organisation']['contactDetails']['emailAddress'],
            'operatorAddress' => $inspectionRequest['licence']['organisation']['contactDetails']['address'],
            'contactPhoneNumbers' => [], // @TODO!
            'tradingNames' => $tradingNames,
            'workshopIsExternal' => true, // @TODO!
            'safetyInspectionVehicles' => 7, // @TODO!
            'safetyInspectionTrailers' => 14, // @TODO!
            'inspectionProvider' => isset($workshop['contactDetails']) ? $workshop['contactDetails'] : [],
            'people' => $people,
            'otherLicences' => [], // @TODO!
            'applicationOperatingCentres' => [], // @TODO!
        ];

        $this->setVariables($data);

        return $this;
    }

    protected function getTotAuthVehicles($inspectionRequest)
    {
        if (!empty($inspectionRequest['application'])) {
            return $inspectionRequest['application']['totAuthVehicles'];
        }
        return $inspectionRequest['licence']['totAuthVehicles'];
    }

    protected function getTotAuthTrailers($inspectionRequest)
    {
        if (!empty($inspectionRequest['application'])) {
            return $inspectionRequest['application']['totAuthTrailers'];
        }
        return $inspectionRequest['licence']['totAuthTrailers'];
    }
}
