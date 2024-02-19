<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\DashboardTmActionLink;
use Common\View\Helper\TranslateReplace;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see DashboardTmActionLink
 */
class DashboardTmActionLinkTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $translator;
    protected $viewHelperManager;
    protected $router;
    protected $request;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->translator = m::mock(TranslatorDelegator::class);
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->sut = new DashboardTmActionLink($this->urlHelper, $this->viewHelperManager, $this->translator);
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
        $this->translator->expects('translate')
            ->with('dashboard.tm-applications.table.action.' . $expectTextKey)
            ->andReturn('LINK TEXT');


        $mockTranslateReplace = m::mock(TranslateReplace::class);
        $mockTranslateReplace->expects('__invoke')
            ->with('dashboard.tm-applications.table.aria.' . $expectTextKey, [323])
            ->andReturn('ARIA');

        $this->viewHelperManager->shouldReceive('get')->with('translateReplace')->andReturn($mockTranslateReplace);

        $this->urlHelper->shouldReceive('fromRoute')
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
            $this->sut->format($data, $column)
        );
    }
}
