<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\Service\Table\Formatter\IrhpPermitNumberInternal;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * IrhpPermitNumberInternal test
 */
class IrhpPermitNumberInternalTest extends MockeryTestCase
{
    public function testFormat()
    {
        $licenceId = 200;
        $irhpPermitTypeId = RefData::ECMT_PERMIT_TYPE_ID;

        $row = [
            'permitNumber' => '4>',
            'irhpPermitApplication' => [
                'relatedApplication' => [
                    'licence' => [
                        'id' => $licenceId,
                    ],
                ],
            ],
            'irhpPermitRange' => [
                'irhpPermitStock' => [
                    'irhpPermitType' => [
                        'id' => $irhpPermitTypeId,
                    ],
                ],
            ],
        ];

        $expectedParams = [
            'licence' => $licenceId
        ];
        $expectedOptions = [
            'query' => ['irhpPermitType' => $irhpPermitTypeId]
        ];
        $expectedOutput = '<a href="INTERNAL_IRHP_URL">4&gt;</a>'; //escaped as proved by &gt;

        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-permits/permit', $expectedParams, $expectedOptions)
            ->once()
            ->andReturn('INTERNAL_IRHP_URL');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->once()->with('Helper\Url')->andReturn($urlHelper);

        $sut = new IrhpPermitNumberInternal();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }
}
