<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\Service\Table\Formatter\InternalLicencePermitReference;
use LmcRbacMvc\Service\AuthorizationService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * InternalLicencePermitReference test
 */
class InternalLicencePermitReferenceTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $authService;
    protected $sut;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelper::class);
        $this->authService = m::mock(AuthorizationService::class);
        $this->sut = new InternalLicencePermitReference($this->urlHelper, $this->authService);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    public function testFormat()
    {
        $appId = 4;
        $licenceId = 200;
        $expectedOutput = '<a class="govuk-link" href="INTERNAL_IRHP_URL">OB1234567/4&gt;</a>'; //escaped as proved by &gt;

        $row = [
            'id' => $appId,
            'licenceId' => $licenceId,
            'applicationRef' => 'OB1234567/4>',
        ];

        $routeParams = [
            'action' => 'edit',
            'irhpAppId' => $appId,
            'licence' => $licenceId
        ];

        $this->urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-application/application', $routeParams)
            ->once()
            ->andReturn('INTERNAL_IRHP_URL');


        $this->assertEquals(
            $expectedOutput,
            $this->sut->format($row, null)
        );
    }
}
