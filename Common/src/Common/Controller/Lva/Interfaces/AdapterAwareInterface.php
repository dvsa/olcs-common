<?php

/**
 * Adapter Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

/**
 * Adapter Aware Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface AdapterAwareInterface
{
    /**
     * @return AdapterInterface
     */
    public function getAdapter();

    public function setAdapter(AdapterInterface $adapter);
}
