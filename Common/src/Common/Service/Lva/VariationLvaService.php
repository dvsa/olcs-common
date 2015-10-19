<?php

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Lva;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function addVariationMessage($licenceId, $redirectRoute = null)
    {
        $link = $this->getVariationLink($licenceId, $redirectRoute);

        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace('variation-message', [$link]);

        $this->getServiceLocator()->get('Helper\Guidance')->append($message);
    }

    public function getVariationLink($licenceId, $redirectRoute = null)
    {
        return $this->getServiceLocator()->get('Helper\Url')
            ->fromRoute('lva-licence/variation', ['licence' => $licenceId, 'redirectRoute' => $redirectRoute]);
    }
}
