<?php

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Email;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\View\Model\InspectionRequestEmailViewModel;
use Zend\View\Model\ViewModel;

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Email implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function sendEmail($to, $subject, $body)
    {
        //@todo
    }

    /**
     * Send an inspection request email
     *
     * @param int $inspectionRequestId
     */
    public function sendInspectionRequestEmail($inspectionRequestId)
    {
        $inspectionRequest = $this->getServiceLocator()->get('Entity\InspectionRequest')
            ->getInspectionRequest($inspectionRequestId);

        $user = $this->getServiceLocator()->get('Entity\User')
            ->getCurrentUser();

        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $peopleData = $this->getServiceLocator()->get('Entity\Person')
            ->getAllForOrganisation($inspectionRequest['licence']['organisation']['id']);

        $workshop = $this->getServiceLocator()->get('Entity\Workshop')
            ->getForLicence($inspectionRequest['licence']['id']);

        // @Todo move this to ViewModel
        // $view = new InspectionRequestEmailViewModel();
        // $view->populate($inspectionRequest, $user, $peopleData, $workshop);


        $people = [];
        if (isset($peopleData['Results']) && !empty($peopleData['Results'])) {
            $people =  array_map(
                function($peopleResult) {
                    return $peopleResult['person'];
                },
                $peopleData['Results']
            );
        }

        $tradingNames = [];
        if (!empty($inspectionRequest['licence']['organisation']['tradingNames'])) {
            $tradingNames = array_map(
                function($tradingName) {
                    return $tradingName['name'];
                },
                $inspectionRequest['licence']['organisation']['tradingNames']
            );
        }

        $requestDate = new \DateTime($inspectionRequest['requestDate']);
        $dueDate =  new \DateTime($inspectionRequest['dueDate']);
        $expiryDate = new \DateTime($inspectionRequest['licence']['expiryDate']);

        $data = [
            'inspectionRequestId' => '189781',
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
        // var_dump($user, $inspectionRequest, $people, $workshop, $data);

        $view = new ViewModel();
        $view->setTemplate('email/inspection-request');
        $view->setVariables($data);

        $emailBody = $this->getServiceLocator()->get('ViewRenderer')->render($view);

        // var_dump($emailBody); exit;
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
