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

    /**
     * Dump email to a file while we decide how the email architecture will work
     */
    public function sendEmail($to, $subject, $body)
    {
        //@todo swap out this for configurable adapter
        $filename = tempnam(sys_get_temp_dir(), 'email');

        $content = <<<EMAIL
To: $to
Subject: $subject

$body
EMAIL;
        file_put_contents($filename, $content);
        $this->getServiceLocator()->get('Zend\Log')->info("Email logged to ".$filename);
    }

    /**
     * Send an inspection request email
     *
     * @param int $inspectionRequestId
     */
    public function sendInspectionRequestEmail($inspectionRequestId)
    {
        // retrieve Inspection Request, User, People and Workshop data
        $inspectionRequest = $this->getServiceLocator()->get('Entity\InspectionRequest')
            ->getInspectionRequest($inspectionRequestId);

        $user = $this->getServiceLocator()->get('Entity\User')
            ->getCurrentUser();

        $peopleData = $this->getServiceLocator()->get('Entity\Person')
            ->getAllForOrganisation($inspectionRequest['licence']['organisation']['id']);

        $workshops = $this->getServiceLocator()->get('Entity\Workshop')
            ->getForLicence($inspectionRequest['licence']['id']);

        // Use view rendering to build email body
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $view = new InspectionRequestEmailViewModel();
        $view->populate($inspectionRequest, $user, $peopleData, $workshops, $translator);
        $emailBody = $this->getServiceLocator()->get('ViewRenderer')->render($view);

        // build subject line
        $subject = sprintf(
            "[ Maintenance Inspection ] REQUEST=%s,STATUS=",
            $inspectionRequest['id']
        );

        // look up destination email address from relevant enforcement area
        $toEmailAddress = $inspectionRequest['licence']['enforcementArea']['emailAddress'];

        return $this->sendEmail($toEmailAddress, $subject, $emailBody);
    }
}
