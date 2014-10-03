<?php

/**
 * External Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\VehicleSafety;

use Common\Controller\Service\VehicleSafety\ExternalLicenceDiscsPsvSectionService;

/**
 * External Licence Discs Psv Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalLicenceDiscsPsvSectionServiceTest extends AbstractLicenceDiscsPsvSectionServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ExternalLicenceDiscsPsvSectionService();

        parent::setUp();
    }
}
