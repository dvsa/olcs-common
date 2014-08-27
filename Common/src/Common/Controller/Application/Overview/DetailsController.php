<?php

/**
 * Overview Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\Overview;

/**
 * Overview Details Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DetailsController extends OverviewController
{
    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }
}
