<?php

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Lva;

use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Variation LVA service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationLvaService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * add variation message
     *
     * @param int         $licenceId     licence id
     * @param null|string $redirectRoute route for redirect
     * @param string      $msgKey        message key
     *
     * @return void
     */
    public function addVariationMessage($licenceId, $redirectRoute = null, $msgKey = 'variation-message')
    {
        $link = $this->getVariationLink($licenceId, $redirectRoute);

        $message = $this->getServiceLocator()->get('Helper\Translation')
            ->translateReplace($msgKey, [$link]);

        $this->getServiceLocator()->get('Helper\Guidance')->append($message);
    }

    /**
     * get variation link
     *
     * @param int         $licenceId     licence id
     * @param string|null $redirectRoute route for redirect
     *
     * @return string
     */
    public function getVariationLink($licenceId, $redirectRoute = null)
    {
        return $this->getServiceLocator()->get('Helper\Url')
            ->fromRoute('lva-licence/variation', ['licence' => $licenceId, 'redirectRoute' => $redirectRoute]);
    }
}
