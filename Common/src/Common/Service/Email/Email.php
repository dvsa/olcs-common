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

        $view = new InspectionRequestEmailViewModel();
        $view->populate($inspectionRequest, $user, $peopleData, $workshop, $translator);

        $emailBody = $this->getServiceLocator()->get('ViewRenderer')->render($view);

        var_dump($emailBody); exit;
    }
}
