<?php

/**
 * External Application Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use Common\Controller\Service\OperatingCentre\ExternalApplicationAuthorisationSectionService;

/**
 * External Application Authorisation Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalApplicationAuthorisationSectionServiceTest extends AbstractApplicationAuthorisationSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\OperatingCentre\ExternalApplicationAuthorisationSectionServiceTest
     */
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ExternalApplicationAuthorisationSectionService();

        parent::setUp();
    }
}
