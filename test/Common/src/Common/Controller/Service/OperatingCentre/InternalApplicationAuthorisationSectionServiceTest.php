<?php

/**
 * Internal Application Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use Common\Controller\Service\OperatingCentre\InternalApplicationAuthorisationSectionService;

/**
 * Internal Application Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class InternalApplicationAuthorisationSectionServiceTest extends AbstractApplicationAuthorisationSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\OperatingCentre\InternalApplicationAuthorisationSectionServiceTest
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new InternalApplicationAuthorisationSectionService();

        parent::setUp();
    }
}
