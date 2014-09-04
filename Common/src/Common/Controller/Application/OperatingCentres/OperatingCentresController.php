<?php

/**
 * OperatingCentres Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\OperatingCentres;

use Common\Controller\Application\Application\ApplicationController;

/**
 * OperatingCentres Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentresController extends ApplicationController
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
