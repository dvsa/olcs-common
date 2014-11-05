<?php

/**
 * Abstract Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractHelperService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
}
