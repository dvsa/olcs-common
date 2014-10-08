<?php

/**
 * Internal Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

use Common\Controller\Service\VehicleSafety\InternalLicenceDiscsPsvSectionService;

/**
 * Internal Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class InternalLicenceDiscsPsvSectionServiceTest extends AbstractLicenceDiscsPsvSectionServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new InternalLicenceDiscsPsvSectionService();

        parent::setUp();
    }
}
