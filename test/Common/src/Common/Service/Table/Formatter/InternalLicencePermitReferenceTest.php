<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\InternalLicencePermitReference;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * InternalLicencePermitReference test
 */
class InternalLicencePermitReferenceTest extends MockeryTestCase
{
    /**
     * @dataProvider scenariosProvider
     */
    public function testFormat($row, $expectedOutput)
    {
        $urlHelper = m::mock(UrlHelper::class);
        $urlHelper->shouldReceive('fromRoute')
            ->with('licence/permits/application', ['action' => 'edit', 'permitid' => 3, 'licence' => 200])
            ->andReturn('INTERNAL_ECMT_URL')
            ->shouldReceive('fromRoute')
            ->with('licence/irhp-application/application', ['action' => 'edit', 'irhpAppId' => 4, 'licence' => 200])
            ->andReturn('INTERNAL_IRHP_URL');

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('Helper\Url')->andReturn($urlHelper);

        $sut = new InternalLicencePermitReference();
        $this->assertEquals(
            $expectedOutput,
            $sut->format($row, null, $sm)
        );
    }

    public function scenariosProvider()
    {
        return [
            'ECMT Annual' => [
                [
                    'id' => 3,
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID,
                    'licenceId' => 200,
                    'applicationRef' => 'ECMT>1234567',
                ],
                '<a href="INTERNAL_ECMT_URL">ECMT&gt;1234567</a>'
            ],
            'ECMT Short Term' => [
                [
                    'id' => 4,
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID,
                    'licenceId' => 200,
                    'applicationRef' => 'IRHP>1234567',
                ],
                '<a href="INTERNAL_IRHP_URL">IRHP&gt;1234567</a>'
            ],
            'IRHP Bilateral' => [
                [
                    'id' => 4,
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID,
                    'licenceId' => 200,
                    'applicationRef' => 'IRHP>1234567',
                ],
                '<a href="INTERNAL_IRHP_URL">IRHP&gt;1234567</a>'
            ],
            'IRHP Multilateral' => [
                [
                    'id' => 4,
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID,
                    'licenceId' => 200,
                    'applicationRef' => 'IRHP>1234567',
                ],
                '<a href="INTERNAL_IRHP_URL">IRHP&gt;1234567</a>'
            ],
            'unknown' => [
                [
                    'id' => 1,
                    'typeId' => 'unknown',
                    'licenceId' => 200,
                    'applicationRef' => 'UNKNOWN>1234567',
                ],
                'UNKNOWN&gt;1234567'
            ],
        ];
    }
}
