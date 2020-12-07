<?php

/**
 * Guidance Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Helper;

use Laminas\ServiceManager\ServiceLocatorAwareInterface;
use Laminas\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Guidance Helper Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GuidanceHelperService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function append($message)
    {
        $placeholder = $this->getServiceLocator()->get('ViewHelperManager')->get('placeholder');
        $placeholder->getContainer('guidance')->append($message);
    }
}
