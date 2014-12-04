<?php

/**
 * Controller Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use \Zend\Mvc\Controller\AbstractController;

/**
 * Controller Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ControllerAwareTrait
{
    protected $controller;

    public function setController(AbstractController $controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
