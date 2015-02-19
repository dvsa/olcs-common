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

    private $excludeTypes = [
        OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
        OrganisationEntityService::ORG_TYPE_PARTNERSHIP
    ];

    public function maybeAddVariationMessage($controller, $orgId)
    {
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getType($orgId);

        if (in_array($orgData['type']['id'], $this->excludeTypes)) {
            return;
        }

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
