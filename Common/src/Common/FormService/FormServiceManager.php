<?php

namespace Common\FormService;

use Laminas\Mvc\Controller\PluginManager;

class FormServiceManager extends PluginManager
{
    // The Abstract Factory in common, selfserve and internal validates the requested plugins
    public function validate($instance)
    {
    }
}
