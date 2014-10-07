<?php

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Common\Util;

/**
 * AbstractLvaController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLvaController extends AbstractActionController
{
    use Util\HelperServiceAware,
        Util\EntityServiceAware;
}
