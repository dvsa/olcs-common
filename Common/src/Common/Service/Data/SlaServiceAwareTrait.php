<?php
namespace Common\Service\Data;

use Common\Service\Data\Sla as SlaService;

/**
 * Class Sla
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
trait SlaServiceAwareTrait
{
    /**
     * @var \Common\Service\Data\Sla
     */
    protected $slaService;

    /**
     * Gets the SLA service.
     *
     * @return \Common\Service\Data\Sla
     */
    public function getSlaService()
    {
        return $this->slaService;
    }

    /**
     * Sets the SLA service.
     *
     * @param SlaService $slaService
     * @return \Common\Service\Data\Sla
     */
    public function setSlaService(SlaService $slaService)
    {
        $this->slaService = $slaService;
        return $this;
    }
}
