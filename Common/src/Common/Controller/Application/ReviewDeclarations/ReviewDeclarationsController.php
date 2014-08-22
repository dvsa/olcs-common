<?php

/**
 * ReviewDeclarations Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\ReviewDeclarations;

use Common\Controller\Application\ApplicationController;

/**
 * ReviewDeclarations Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReviewDeclarationsController extends ApplicationController
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
