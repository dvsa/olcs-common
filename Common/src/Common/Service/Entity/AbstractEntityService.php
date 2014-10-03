<?php

/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity;

use Common\Util\HelperServiceAware;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;


/**
 * Abstract Entity Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractEntityService implements ServiceLocatorAwareInterface
{
    use HelperServiceAware,
        ServiceLocatorAwareTrait;
}
