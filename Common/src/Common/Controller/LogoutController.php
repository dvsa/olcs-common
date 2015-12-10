<?php

/**
 * Logout Controller
 */
namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

/**
 * Logout Controller
 */
class LogoutController extends AbstractActionController
{
    public function indexAction()
    {
        $session = new Container();
        $session->getManager()->destroy(['clear_storage' => true, 'send_expire_cookie' => true]);

        // redirs to the openAM logout page
        return $this->redirect()->toUrl('/secure/UI/Logout');
    }
}
