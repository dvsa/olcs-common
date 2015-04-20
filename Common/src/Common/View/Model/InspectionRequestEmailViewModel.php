<?php

/**
 * Inspect Request Email View Model
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\View\Model;

use Zend\View\Model\ViewModel;
use Common\Controller\Lva\Adapters\AbstractOperatingCentreAdapter as OperatingCentre;

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
    public function populate($inspectionRequest, $user, $peopleData, $workshops, $translator)
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

        // use first workshop only
        $workshop = array_shift($workshops);

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
            'licenceType' => $this->getLicenceType($inspectionRequest, $translator),
            'totAuthVehicles' => $this->getTotAuthVehicles($inspectionRequest),
            'totAuthTrailers' => $this->getTotAuthTrailers($inspectionRequest),
            'numberOfOperatingCentres' => count($inspectionRequest['licence']['operatingCentres']),
            'expiryDate' => $expiryDate->format('d/m/Y'),
            'operatorId' => $inspectionRequest['licence']['organisation']['id'],
            'operatorName' => $inspectionRequest['licence']['organisation']['name'],
            'operatorEmail' => $inspectionRequest['licence']['correspondenceCd']['emailAddress'],
            'operatorAddress' => $inspectionRequest['licence']['correspondenceCd']['address'],
            'contactPhoneNumbers' => $inspectionRequest['licence']['correspondenceCd']['phoneContacts'],
            'tradingNames' => $tradingNames,
            'workshopIsExternal' => (isset($workshop['isExternal']) && $workshop['isExternal'] === 'Y'),
            'safetyInspectionVehicles' => $inspectionRequest['licence']['safetyInsVehicles'],
            'safetyInspectionTrailers' => $inspectionRequest['licence']['safetyInsTrailers'],
            'inspectionProvider' => isset($workshop['contactDetails']) ? $workshop['contactDetails'] : [],
            'people' => $people,
            'otherLicences' => $this->getOtherLicences($inspectionRequest),
            'applicationOperatingCentres' => $this->getApplicationOperatingCentres($inspectionRequest),
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

    protected function getLicenceType($inspectionRequest, $translator)
    {
        if (!empty($inspectionRequest['application'])) {
            return $translator->translate($inspectionRequest['application']['licenceType']['id']);
        }
        return $translator->translate($inspectionRequest['licence']['licenceType']['id']);
    }

    protected function getOtherLicences($inspectionRequest)
    {
        $licenceNos = array_map(
            function ($licence) {
                return $licence['licNo'];
            },
            $inspectionRequest['licence']['organisation']['licences']
        );

        $currentLicNo = $inspectionRequest['licence']['licNo'];
        return array_filter(
            $licenceNos,
            function ($licNo) use ($currentLicNo) {
                return ($licNo !== $currentLicNo) && !empty($licNo);
            }
        );
    }

    protected function getApplicationOperatingCentres($inspectionRequest)
    {
        if (isset($inspectionRequest['application']['operatingCentres'])) {
            $aocs = array_map(
                function ($aoc) {
                    switch ($aoc['action']) {
                        case OperatingCentre::ACTION_ADDED:
                            $aoc['action'] = 'Added';
                            break;
                        case OperatingCentre::ACTION_EXISTING:
                            $aoc['action'] = 'Existing';
                            break;
                        case OperatingCentre::ACTION_CURRENT:
                            $aoc['action'] = 'Current';
                            break;
                        case OperatingCentre::ACTION_UPDATED:
                            $aoc['action'] = 'Updated';
                            break;
                        case OperatingCentre::ACTION_DELETED:
                            $aoc['action'] = 'Deleted';
                            break;
                    }
                    return $aoc;
                },
                $inspectionRequest['application']['operatingCentres']
            );
            return $aocs;
        }
        return [];
    }
}
