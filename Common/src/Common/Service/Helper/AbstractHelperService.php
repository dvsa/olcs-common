<?php

/**
 * Abstract Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Laminas\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractHelperService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
}
