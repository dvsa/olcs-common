<?php

/**
 * Internal Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

use Common\Controller\Service\VehicleSafety\InternalVariationDiscsPsvSectionService;

/**
 * Internal Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class InternalVariationDiscsPsvSectionServiceTest extends AbstractVariationDiscsPsvSectionServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new InternalVariationDiscsPsvSectionService();

        parent::setUp();
    }
}
