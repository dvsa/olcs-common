<?php

namespace CommonTest\Data\Object\Search;

use Common\Data\Object\Search\Cases;
use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class CasesTest
 * @package CommonTest\Data\Object\Search
 */
class CasesTest extends SearchAbstractTest
{
    protected $class = Cases::class;

    /**
     * @dataProvider dpTestCaseIdFormatter
     */
    public function testCaseIdFormatter($expected, $row, $isIrhpAdmin)
    {
        $column = [];

        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $authService = m::mock(AuthorizationService::class);
        $authService->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)
            ->once()
            ->andReturn($isIrhpAdmin);

        $serviceLocator->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->once()
            ->andReturn($authService);

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[1]['formatter']($row, $column, $serviceLocator));
    }

    public function dpTestCaseIdFormatter()
    {
        return [
            // expected, row, isIrhpAdmin
            [
                '<a class="govuk-link" href="/case/details/123>">123&gt;</a>',
                [
                    'caseId' => '123>',
                ],
                false,
            ],
            [
                '123&gt;',
                [
                    'caseId' => '123>',
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider dpTestNameFormatter
     */
    public function testNameFormatter($expected, $row, $isIrhpAdmin)
    {
        $column = [];

        $serviceLocator = m::mock(ServiceLocatorInterface::class);

        $urlHelper = m::mock(UrlHelperService::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('operator/business-details', ['organisation' => 123])
            ->andReturn('ORG_URL')
            ->shouldReceive('fromRoute')
            ->with('transport-manager/details', ['transportManager' => 123])
            ->andReturn('TM_URL');

        $serviceLocator->shouldReceive('get')
            ->with('Helper\Url')
            ->once()
            ->andReturn($urlHelper);

        $authService = m::mock(AuthorizationService::class);
        $authService->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)
            ->once()
            ->andReturn($isIrhpAdmin);

        $serviceLocator->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->once()
            ->andReturn($authService);

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[7]['formatter']($row, $column, $serviceLocator));
    }

    public function dpTestNameFormatter()
    {
        return [
            // expected, row, isIrhpAdmin
            [
                '<a class="govuk-link" href="ORG_URL">org name&gt;</a>',
                [
                    'orgId' => 123,
                    'orgName' => 'org name>',
                ],
                false,
            ],
            [
                'org name&gt;',
                [
                    'orgId' => 123,
                    'orgName' => 'org name>',
                ],
                true,
            ],
            [
                '<a class="govuk-link" href="TM_URL">forename&gt; family name&gt;</a>',
                [
                    'tmId' => 123,
                    'tmForename' => 'forename>',
                    'tmFamilyName' => 'family name>',
                ],
                false,
            ],
            [
                'forename&gt; family name&gt;',
                [
                    'tmId' => 123,
                    'tmForename' => 'forename>',
                    'tmFamilyName' => 'family name>',
                ],
                true,
            ],
        ];
    }
}
