<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\DashboardTmActionLink;
use Common\View\Helper\TranslateReplace;
use Laminas\Mvc\I18n\Translator;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see DashboardTmActionLink
 */
class DashboardTmActionLinkTest extends MockeryTestCase
{
    /* @var \Mockery\MockInterface */
    private $mockSm;

    public function setUp(): void
    {
        $this->mockSm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
    }

    public function dataProviderFormat()
    {
        return [
            [
                'statusId' => RefData::TMA_STATUS_AWAITING_SIGNATURE,
                'isVariation' => true,
                'expectTextKey' => 'provide-details',
            ],
            [
                RefData::TMA_STATUS_INCOMPLETE,
                'isVariation' => false,
                'provide-details',
            ],
            [
                RefData::TMA_STATUS_OPERATOR_SIGNED,
                'isVariation' => false,
                'view-details',
            ],
            [
                RefData::TMA_STATUS_POSTAL_APPLICATION,
                'isVariation' => false,
                'view-details',
            ],
            [
                RefData::TMA_STATUS_TM_SIGNED,
                'isVariation' => false,
                'view-details',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderFormat
     */
    public function testFormat($statusId, $isVariation, $expectTextKey)
    {
        $translationHelper = m::mock(Translator::class);
        $translationHelper->expects('translate')
            ->with('dashboard.tm-applications.table.action.' . $expectTextKey)
            ->andReturn('LINK TEXT');

        $this->mockSm->expects('get')->with('translator')->andReturn($translationHelper);

        $mockTranslateReplace = m::mock(TranslateReplace::class);
        $mockTranslateReplace->expects('__invoke')
            ->with('dashboard.tm-applications.table.aria.' . $expectTextKey, [323])
            ->andReturn('ARIA');

        $viewHelperManager = m::mock(HelperPluginManager::class);
        $viewHelperManager->expects('get')->with('translateReplace')->andReturn($mockTranslateReplace);

        $this->mockSm
            ->expects('get')
            ->with('ViewHelperManager')
            ->andReturn($viewHelperManager);

        $urlHelperService = m::mock(UrlHelperService::class);
        $urlHelperService->expects('fromRoute')
            ->with(
                (
                    $isVariation
                    ? 'lva-variation/transport_manager_details'
                    : 'lva-application/transport_manager_details'
                ),
                [
                    'action' => null,
                    'application' => 323,
                    'child_id' => 12.,
                ],
                [],
                true
            )
            ->andReturn('http://url.com');

        $this->mockSm->expects('get')->with('Helper\Url')->andReturn($urlHelperService);

        $data = [
            'applicationId' => 323,
            'transportManagerApplicationStatus' => [
                'id' => $statusId,
                'description' => 'FooBar',
            ],
            'transportManagerApplicationId' => 12,
            'isVariation' => $isVariation,
        ];
        $column = [];

        static::assertEquals(
            '<a class="govuk-link" href="http://url.com" aria-label="ARIA">LINK TEXT</a>',
            DashboardTmActionLink::format($data, $column, $this->mockSm)
        );
    }
}
