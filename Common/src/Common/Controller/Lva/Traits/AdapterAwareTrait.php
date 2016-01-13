<?php

/**
 * Adapter Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Common\Controller\Lva\Interfaces\AdapterInterface;

/**
 * Adapter Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait AdapterAwareTrait
{
    protected $adapter;

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
}
