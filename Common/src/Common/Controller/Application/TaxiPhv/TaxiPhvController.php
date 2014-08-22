<?php

/**
 * TaxiPhv Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\TaxiPhv;

use Common\Controller\Application\ApplicationController;

/**
 * TaxiPhv Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaxiPhvController extends ApplicationController
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
