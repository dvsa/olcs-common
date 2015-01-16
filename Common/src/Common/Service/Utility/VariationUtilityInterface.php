<?php

/**
 * Variation Utility Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Utility;

/**
 * Variation Utility Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface VariationUtilityInterface
{
    /**
     * Alter the create variation data
     *
     * @param array $data
     * @return array
     */
    public function alterCreateVariationData(array $data);
}
