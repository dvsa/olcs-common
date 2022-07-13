<?php

namespace CommonTest\Data\Object\Search;

use Common\Data\Object\Search\Licence;
use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Class LicenceTest
 * @package CommonTest\Data\Object\Search
 */
class LicenceTest extends SearchAbstractTest
{
    protected $class = Licence::class;

    /**
     * @dataProvider dpTestCaseCountFormatter
     */
    public function testCaseCountFormatter($expected, $row, $isIrhpAdmin)
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
        $this->assertSame($expected, $columns[7]['formatter']($row, $column, $serviceLocator));
    }

    public function dpTestCaseCountFormatter()
    {
        return [
            // expected, row, isIrhpAdmin
            [
                '<a class="govuk-link" href="/licence/123/cases">7&gt;</a>',
                [
                    'licId' => 123,
                    'caseCount' => '7>'
                ],
                false,
            ],
            [
                '7&gt;',
                [
                    'licId' => 123,
                    'caseCount' => '7>'
                ],
                true,
            ],
        ];
    }
}
