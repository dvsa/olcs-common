<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\LicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Licence permit reference test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class LicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('permits/application-overview', ['id' => 3])
            ->andReturn('http://selfserve/permits/application-overview/3')
            ->shouldReceive('fromRoute')
            ->with('permits/ecmt-under-consideration', ['id' => 5])
            ->andReturn('http://selfserve/permits/ecmt-under-consideration/5')
            ->shouldReceive('fromRoute')
            ->with('permits/ecmt-awaiting-fee', ['id' => 7])
            ->andReturn('http://selfserve/permits/ecmt-awaiting-fee/7')
            ->shouldReceive('fromRoute')
            ->with('permits/ecmt-valid-permits', ['id' => 9])
            ->andReturn('http://selfserve/permits/ecmt-valid-permits/9')
            ->shouldReceive('fromRoute')
            ->with('permits/application', ['id' => 100])
            ->andReturn('http://selfserve/permits/application/100');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);

        $sut = new LicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            [
                [
                    'id' => '3',
                    'applicationRef' => 'ECMT>1234567',
                    'isUnderConsideration' => false,
                    'isAwaitingFee' => false,
                    'isFeePaid' => false,
                    'isIssueInProgress' => false,
                    'isValid' => false,
                ],
                '<a class="overview__link" href="http://selfserve/permits/application-overview/3">' .
                    '<span class="overview__link--underline">ECMT&gt;1234567</span></a>'
            ],
            [
                [   'id' => '5',
                    'applicationRef' => 'ECMT>2345678',
                    'isUnderConsideration' => true,
                    'isAwaitingFee' => false,
                    'isFeePaid' => false,
                    'isIssueInProgress' => false,
                    'isValid' => false,
                ],
                '<a class="overview__link" href="http://selfserve/permits/ecmt-under-consideration/5">' .
                    '<span class="overview__link--underline">ECMT&gt;2345678</span></a>'
            ],
            [
                [
                    'id' => '7',
                    'applicationRef' => 'ECMT>3456789',
                    'isUnderConsideration' => false,
                    'isAwaitingFee' => true,
                    'isFeePaid' => false,
                    'isIssueInProgress' => false,
                    'isValid' => false,
                ],
                '<a class="overview__link" href="http://selfserve/permits/ecmt-awaiting-fee/7">' .
                    '<span class="overview__link--underline">ECMT&gt;3456789</span></a>'
            ],
            [
                [
                    'id' => '8',
                    'applicationRef' => 'ECMT>3456789',
                    'isUnderConsideration' => false,
                    'isAwaitingFee' => false,
                    'isFeePaid' => true,
                    'isIssueInProgress' => false,
                    'isValid' => false,
                ],
                'ECMT&gt;3456789'
            ],
            [
                [
                    'id' => '8',
                    'applicationRef' => 'ECMT>3456789',
                    'isUnderConsideration' => false,
                    'isAwaitingFee' => false,
                    'isFeePaid' => false,
                    'isIssueInProgress' => true,
                    'isValid' => false,
                ],
                'ECMT&gt;3456789'
            ],
            [
                [
                    'id' => '9',
                    'applicationRef' => 'ECMT>4567890',
                    'isUnderConsideration' => false,
                    'isAwaitingFee' => false,
                    'isFeePaid' => false,
                    'isIssueInProgress' => false,
                    'isValid' => true,
                ],
                '<a class="overview__link" href="http://selfserve/permits/ecmt-valid-permits/9">' .
                    '<span class="overview__link--underline">ECMT&gt;4567890</span></a>'
            ],
            'IRHP app - not yet submitted' => [
                [
                    'id' => 100,
                    'applicationRef' => 'IRHP>ABC100',
                    'irhpPermitType' => [
                        'id' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    ],
                    'isNotYetSubmitted' => true,
                ],
                '<a class="overview__link" href="http://selfserve/permits/application/100">' .
                    '<span class="overview__link--underline">IRHP&gt;ABC100</span></a>'
            ],
            'IRHP app - any other' => [
                [
                    'id' => 200,
                    'applicationRef' => 'IRHP>ABC200',
                    'irhpPermitType' => [
                        'id' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    ],
                    'isNotYetSubmitted' => false,
                ],
                'IRHP&gt;ABC200'
            ]
        ];
    }
}
