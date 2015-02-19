<?php

/**
 * Licence People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Lva;

use Common\Service\Entity\OrganisationEntityService;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Licence People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicencePeopleLvaService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function addVariationMessage($controller)
    {
        $params = [
            'licence' => $controller->params('licence')
        ];

        $link = $controller->url()->fromRoute('create_variation', $params);
        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace('variation-people-message', [$link]);

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addCurrentInfoMessage($message);
    }
}
