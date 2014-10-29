<?php

/**
 * Total Authorisation Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Entity\Traits;

/**
 * Total Authorisation Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait TotalAuthorisationTrait
{
    protected $totalAuthorisationBundle = array(
        'properties' => array(
            'totAuthVehicles'
        )
    );

    abstract protected function get($id, $bundle = null);

    public function getTotalAuthorisation($id)
    {
        return $this->get($id, $this->totalAuthorisationBundle)['totAuthVehicles'];
    }
}
