<?php

/**
 * Placeholder Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\TransportManagers;

/**
 * Placeholder Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PlaceholderController extends TransportManagersController
{
    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        $view = $this->getViewModel();
        $view->setTemplate('journey/placeholder');
        return $this->renderSection($view);
    }

    /**
     * Placeholder save method
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
    }
}
