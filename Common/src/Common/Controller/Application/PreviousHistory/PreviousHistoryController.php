<?php

/**
 * PreviousHistory Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\PreviousHistory;

use Common\Controller\Application\Application\ApplicationController;

/**
 * PreviousHistory Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PreviousHistoryController extends ApplicationController
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
