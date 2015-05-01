<?php

/**
 * Send an email containing a list of licences that have been set to CNS
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace Common\Service\Email\Message;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Send an email containing a list of licences that have been set to CNS
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ContinuationNotSought implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Send an email containing a list of licences that have been set to CNS
     */
    public function send()
    {
        $endDate = $specifiedDate = $this->getServiceLocator()->get('Helper\Date')->getDate(\DateTime::W3C);
        $time = strtotime($endDate);
        $startDate = date(\DateTime::W3C, strtotime("-1 month", $time));

        $licenceEntityService = $this->getServiceLocator()->get('Entity\Licence');
        $licences = $licenceEntityService->getWhereContinuationNotSought($startDate, $endDate);

        $dateFormatHelper = $this->getServiceLocator()->get('ViewHelperManager')->get('dateFormat');

        $viewContent = new \Zend\View\Model\ViewModel();
        $viewContent->setTemplate('email/continuation-not-sought');
        $viewContent->setVariable('licences', $licences['Results']);
        $viewContent->setVariable(
            'startDate',
            $dateFormatHelper(new \DateTime($startDate), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT)
        );
        $viewContent->setVariable(
            'endDate',
            $dateFormatHelper(new \DateTime($endDate), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT)
        );

        // Put content into the template
        $view = new \Zend\View\Model\ViewModel();
        $view->setTemplate('layout/email');
        $view->setVariable('content', $this->getServiceLocator()->get('ViewRenderer')->render($viewContent));

        // send it
        $this->getServiceLocator()->get('Email')->sendEmail(
            'donotreply@otc.gsi.gov.uk',
            'OLBS eCommerce/LIC/VIA',
            $this->getServiceLocator()->get('Entity\SystemParameter')->getValue(
                \Common\Service\Entity\SystemParameterEntityService::CNS_EMAIL_LIST
            ),
            $this->getServiceLocator()->get('Helper\Translation')->translateReplace(
                'email.cns.subject',
                [$viewContent->getVariable('startDate'), $viewContent->getVariable('endDate')]
            ),
            $this->getServiceLocator()->get('ViewRenderer')->render($view)
        );
    }
}
