<?php

/**
 * TransportManagers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\TransportManagers;

use Common\Controller\Application\ApplicationController;

/**
 * TransportManagers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagersController extends ApplicationController
{
    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->goToFirstSubSection();
    }
}
