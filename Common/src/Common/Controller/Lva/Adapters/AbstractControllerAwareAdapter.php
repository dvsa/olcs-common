<?php

/**
 * Abstract Controller Aware Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\ControllerAwareInterface;
use Common\Controller\Lva\Traits\ControllerAwareTrait;

/**
 * Abstract Controller Aware Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractControllerAwareAdapter extends AbstractAdapter implements ControllerAwareInterface
{
    use ControllerAwareTrait;

    /**
     * We override the parent method here so we can pass in the controller
     *
     * @param string $lva
     * @return AbstractLvaAdapter
     */
    protected function getLvaAdapter($lva)
    {
        return parent::getLvaAdapter($lva)->setController($this->getController());
    }
}
