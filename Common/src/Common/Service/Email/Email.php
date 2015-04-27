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

    public function sendEmail($from, $to, $subject, $body, $html = true)
    {
        return $this->sendEmailViaRestService($from, $to, $subject, $body, $html);
    }

    /**
     * Send email via REST email service
     */
    protected function sendEmailViaRestService($from, $to, $subject, $body, $html)
    {
        $data = compact('from', 'to', 'subject', 'body', 'html');
        return $this->sendPost('email\\', $data);
    }
}
