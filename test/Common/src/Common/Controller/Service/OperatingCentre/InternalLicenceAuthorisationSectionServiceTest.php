<?php

/**
 * Internal Licence Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use Common\Controller\Service\OperatingCentre\InternalLicenceAuthorisationSectionService;

/**
 * Internal Licence Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class InternalLicenceAuthorisationSectionServiceTest extends AbstractLicenceAuthorisationSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\OperatingCentre\InternalLicenceAuthorisationSectionServiceTest
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new InternalLicenceAuthorisationSectionService();

        parent::setUp();
    }
}
