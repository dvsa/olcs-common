<?php

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Email;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Common\Util\RestCallTrait;
use Common\View\Model\InspectionRequestEmailViewModel;

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Email implements ServiceLocatorAwareInterface
{
    use RestCallTrait;
    use ServiceLocatorAwareTrait;

    public function sendEmail($from, $to, $subject, $body)
    {
        return $this->sendEmailViaRestService($from, $to, $subject, $body);
    }

    /**
     * Send email via REST email service
     */
    protected function sendEmailViaRestService($from, $to, $subject, $body)
    {
        $data = compact('from', 'to', 'subject', 'body');
        return $this->sendPost('email\\', $data);
    }

    /**
     * Dump email to a temporary file and log its location, useful for dev
     */
    protected function sendEmailToTmpFile($from, $to, $subject, $body)
    {
        $filename = tempnam(sys_get_temp_dir(), 'email_');

        $content = <<<EMAIL
From: $from
To: $to
Subject: $subject

$body
EMAIL;
        file_put_contents($filename, $content);
        $this->getServiceLocator()->get('Zend\Log')->info("Email logged to ".$filename);
    }
}
