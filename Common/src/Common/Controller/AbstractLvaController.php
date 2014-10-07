<?php

namespace Common\Controller;

use Common\Util;
use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractLvaController extends AbstractActionController
{
    use Util\HelperServiceAware,
        Util\EntityServiceAware;
}
