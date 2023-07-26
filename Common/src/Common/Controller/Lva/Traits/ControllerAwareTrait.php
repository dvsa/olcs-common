<?php

namespace Common\Controller\Lva\Traits;

use Laminas\Mvc\Controller\AbstractController;

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
