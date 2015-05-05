<?php

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Email;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Common\Util\RestCallTrait;

/**
 * Email Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Email implements ServiceLocatorAwareInterface
{
    use RestCallTrait;
    use ServiceLocatorAwareTrait;

    public function sendEmail($fromEmail, $fromName, $to, $subject, $body, $html = true)
    {
        return $this->sendEmailViaRestService($fromEmail, $fromName, $to, $subject, $body, $html);
    }

    public function sendTemplate($fromEmail, $fromName, $to, $subject, $view, $vars, $html = true)
    {
        $content = $this->getServiceLocator()
            ->get('Helper\Translation')
            ->translateReplace($view, $vars);

        // Put content into the template
        $view = new ViewModel();
        $view->setTemplate('layout/email');
        $view->setVariable('content', $content);
        $body = $this->getServiceLocator()->get('ViewRenderer')->render($view);

        return $this->sendEmailViaRestService($fromEmail, $fromName, $to, $subject, $body, $html);
    }

    /**
     * Send email via REST email service
     */
    protected function sendEmailViaRestService($fromEmail, $fromName, $to, $subject, $body, $html)
    {
        $data = compact('fromEmail', 'fromName', 'to', 'subject', 'body', 'html');

        if (empty($data['fromEmail']) || empty($data['fromName'])) {
            $emailConfig = $this->getServiceLocator()->get('config')['email']['default'];
            if (empty($data['fromEmail'])) {
                $data['fromEmail'] = $emailConfig['from_address'];
            }
            if (empty($data['fromName'])) {
                $data['fromName'] = $emailConfig['from_name'];
            }
        }

        return $this->sendPost('email\\', $data);
    }
}
