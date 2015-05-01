<?php

/**
 * Send an email using PHP mail function
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Service\Email;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Send an email using PHP mail function
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class EmailMailFunction implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function sendEmail($fromEmail, $fromName, $to, $subject, $body, $html = true)
    {
        $headers = "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromName} <{$fromEmail}>\r\n";

        if ($html) {
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        }

        mail($to, $subject, $body, $headers);
    }
}
