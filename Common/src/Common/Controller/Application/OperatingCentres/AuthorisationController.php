<?php

/**
 * Authorisation Controller
 *
 * External - Application - Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Controller\Application\OperatingCentres;

use Common\Controller\Traits;

/**
 * Authorisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AuthorisationController extends OperatingCentresController
{
    use Traits\GenericIndexAction,
        Traits\GenericAddAction,
        Traits\GenericEditAction;

    protected $inlineScripts = ['add-operating-centre'];

    protected $sectionServiceName = 'OperatingCentre\\ExternalApplicationAuthorisation';

    /**
     * Delete sub action
     *
     * @return Response
     */
    public function deleteAction()
    {
        if ($this->getSectionService()->getOperatingCentresCount() === 1
            && $this->getActionId()
        ) {
            $this->getSectionService('TrafficArea')->setTrafficArea(null);
        }

        return $this->delete();
    }

    /**
     * Process save crud
     *
     * @param array $data
     */
    protected function processSaveCrud($data)
    {
        if ($this->getSectionService()->setTrafficAreaAfterCrudAction($data) === false) {

            $this->addWarningMessage('select-traffic-area-error');
            $this->setCaughtResponse($this->redirect()->toRoute(null, array(), array(), true));
            return;
        }

        return parent::processSaveCrud($data);
    }
}
