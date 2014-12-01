<?php

/**
 * Redirect
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Plugin;

use Zend\Json\Json;
use Zend\Mvc\Controller\Plugin\Redirect as ZendRedirect;

/**
 * Redirect
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Redirect extends ZendRedirect
{
    public function toRouteAjax($route = null, $params = array(), $options = array(), $reuseMatchedParams = false)
    {
        $controller = $this->getController();

        if ($controller->getRequest()->isXmlHttpRequest()) {
            $data = array(
                'status' => 302,
                'location' => $controller->url()->fromRoute($route, $params, $options, $reuseMatchedParams)
            );
            $this->getResponse()->getHeaders()->addHeaders(['Content-Type' => 'application/json']);
            $this->getResponse()->setContent(Json::encode($data));
            return $this->getResponse();

        } else {

            return $this->toRoute($route, $params, $options, $reuseMatchedParams);
        }
    }
}
