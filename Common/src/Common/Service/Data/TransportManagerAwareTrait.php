<?php

namespace Common\Service\Data;

use Common\Service\Data\TransportManager;

/**
 * Service Class Trait
 *
 * @package Common\Service\Data
 */
trait TransportManagerAwareTrait
{
    /**
     * @var TransportManager
     */
    private $transportManager;

    /**
     * @param TransportManager $transportManager
     */
    public function setTransportManagerService(TransportManager $transportManager)
    {
        $this->transportManager = $transportManager;
    }

    /**
     * @return TransportManager
     */
    public function getTransportManagerService()
    {
        return $this->transportManager;
    }
}
