<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Helper\UrlHelperService as UrlHelper;
use Common\Service\Table\Formatter\IssuedPermitLicencePermitReference;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * IssuedPermitLicencePermitReference test
 */
class IssuedPermitLicencePermitReferenceTest extends MockeryTestCase
{
    private $urlHelper;

    private $sm;

    public function setUp()
    {
        $this->urlHelper = m::mock(UrlHelper::class);

        $this->sm = m::mock(ServiceLocatorInterface::class);

        $this->sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($this->urlHelper);
    }

    /**
     * @dataProvider dpFormatLinkToIssuedPermits
     */
    public function testFormatLinkToIssuedPermits($row, $expectedOutput)
    {
        $this->urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-permits', ['permitid' => $row['id'], 'licence' => $row['licenceId'], 'permitTypeId' => $row['typeId']])
            ->andReturn('http://internal/licence/'.$row['licenceId'].'/permits/'.$row['id'].'/'.$row['typeId'].'/irhp-permits/');

        $this->assertEquals(
            $expectedOutput,
            IssuedPermitLicencePermitReference::format($row, null, $this->sm)
        );
    }

    public function dpFormatLinkToIssuedPermits()
    {
        return [
            [
                [
                    'id' => 3,
                    'licenceId' => 200,
                    'applicationRef' => 'ECMT>1234567',
                    'typeId' => RefData::ECMT_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/200/permits/3/1/irhp-permits/">ECMT&gt;1234567</a>'
            ],
            [
                [
                    'id' => 5,
                    'licenceId' => 202,
                    'applicationRef' => 'ECMT>2345678',
                    'typeId' => RefData::ECMT_SHORT_TERM_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/202/permits/5/2/irhp-permits/">ECMT&gt;2345678</a>'
            ],
            [
                [
                    'id' => 7,
                    'licenceId' => 204,
                    'applicationRef' => 'ECMT>3456789',
                    'typeId' => RefData::ECMT_REMOVAL_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/204/permits/7/3/irhp-permits/">ECMT&gt;3456789</a>'
            ],
            [
                [
                    'id' => 44,
                    'licenceId' => 206,
                    'applicationRef' => 'IRHP>7654321',
                    'typeId' => RefData::IRHP_BILATERAL_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/206/permits/44/4/irhp-permits/">IRHP&gt;7654321</a>'
            ],
            [
                [
                    'id' => 46,
                    'licenceId' => 208,
                    'applicationRef' => 'IRHP>6543210',
                    'typeId' => RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/208/permits/46/5/irhp-permits/">IRHP&gt;6543210</a>'
            ]
        ];
    }

    /**
     * @dataProvider dpFormatLinkToApplication
     */
    public function testFormatLinkToApplication($row, $expectedOutput)
    {
        $this->urlHelper->shouldReceive('fromRoute')
            ->with('licence/irhp-application/application', ['licence' => $row['licenceId'], 'action' => 'edit', 'irhpAppId' => $row['id']])
            ->andReturn('http://internal/licence/'.$row['licenceId'].'/irhp-application/edit/'.$row['id'].'/');

        $this->assertEquals(
            $expectedOutput,
            IssuedPermitLicencePermitReference::format($row, null, $this->sm)
        );
    }

    public function dpFormatLinkToApplication()
    {
        return [
            [
                [
                    'id' => 100010,
                    'licenceId' => 212,
                    'applicationRef' => 'CERT>7654321',
                    'typeId' => RefData::CERT_ROADWORTHINESS_VEHICLE_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/212/irhp-application/edit/100010/">CERT&gt;7654321</a>'
            ],
            [
                [
                    'id' => 100012,
                    'licenceId' => 208,
                    'applicationRef' => 'CERT>6543210',
                    'typeId' => RefData::CERT_ROADWORTHINESS_TRAILER_PERMIT_TYPE_ID
                ],
                '<a href="http://internal/licence/208/irhp-application/edit/100012/">CERT&gt;6543210</a>'
            ]
        ];
    }
}
