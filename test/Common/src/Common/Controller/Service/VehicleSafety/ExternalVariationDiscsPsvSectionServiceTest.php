<?php

/**
 * External Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

use Common\Controller\Service\VehicleSafety\ExternalVariationDiscsPsvSectionService;

/**
 * External Variation Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalVariationDiscsPsvSectionServiceTest extends AbstractVariationDiscsPsvSectionServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ExternalVariationDiscsPsvSectionService();

        parent::setUp();
    }
}
