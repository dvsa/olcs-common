<?php

/**
 * Transport Manager Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Common\View\Model\ReviewViewModel;
use Zend\Mvc\Controller\AbstractActionController as ZendAbstractActionController;

/**
 * Transport Manager Review Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerReviewController extends ZendAbstractActionController
{
    public function indexAction()
    {
        $id = $this->params('id');

        $config = $this->getServiceLocator()->get('Helper\TransportManager')
            ->getReviewConfig($id);

        return new ReviewViewModel($config);
    }
}
