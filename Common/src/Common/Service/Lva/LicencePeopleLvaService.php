<?php

/**
 * Licence People LVA service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Service\Lva;

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

        $link = $controller->url()->fromRoute('lva-licence/variation', $params);
        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace('variation-people-message', [$link]);

        $placeholder = $this->getServiceLocator()->get('ViewHelperManager')->get('placeholder');

        $placeholder->getContainer('guidance')->append($message);
    }
}
