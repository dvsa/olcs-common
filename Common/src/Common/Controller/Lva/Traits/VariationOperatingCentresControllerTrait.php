<?php

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Command\Variation\RestoreOperatingCentre;

/**
 * Common variation OC controller logic
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait VariationOperatingCentresControllerTrait
{
    public function restoreAction()
    {
        $data = [
            'id' => $this->params('child_id'),
            'application' => $this->getIdentifier()
        ];

        $response = $this->handleCommand(RestoreOperatingCentre::create($data));

        if ($response->isOk()) {
            return $this->redirect()->toRouteAjax(null, ['action' => null, 'child_id' => null], [], true);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addUnknownError();
        } else {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('Can\'t restore this record');
        }

        return $this->redirect()->toRouteAjax(null, ['action' => null, 'child_id' => null], [], true);
    }
}
