<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\CaseEntityNrStatus;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Service\Table\Formatter\CaseEntityNrStatus
 */
class CaseEntityNrStatusTest extends MockeryTestCase
{
    /** @var  \Common\Service\Helper\UrlHelperService | \Zend\ServiceManager\ServiceLocatorInterface */
    private $mockSm;
    /** @var  \Common\Service\Helper\UrlHelperService | m\MockInterface */
    private $mockUrlHlp;

    public function setUp()
    {
        $this->mockUrlHlp = m::mock(\Common\Service\Helper\UrlHelperService::class);

        $this->mockSm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $this->mockSm->shouldReceive('get')->with('Helper\Url')->andReturn($this->mockUrlHlp);
    }

    public function testFormatTm()
    {
        $tmId = 9999;

        $this->mockUrlHlp
            ->shouldReceive('fromRoute')
            ->with('transport-manager', ['transportManager' => $tmId])
            ->once()
            ->andReturn('EXPECT_URL');

        $data = [
            'caseType' => [
                'id' => \Common\RefData::CASE_TYPE_TM,
            ],
            'transportManager' => [
                'id' => $tmId,
            ],
        ];

        static::assertSame(
            '<a href="EXPECT_URL">' . $tmId . '</a>',
            CaseEntityNrStatus::format($data, null, $this->mockSm)
        );
    }

    public function testFormatLic()
    {
        $licId = 9999;

        $this->mockUrlHlp
            ->shouldReceive('fromRoute')
            ->with('lva-licence', ['licence' => $licId])
            ->once()
            ->andReturn('EXPECT_LIC_URL');

        $data = [
            'caseType' => [
                'id' => \Common\RefData::CASE_TYPE_LICENCE,
            ],
            'licence' => [
                'id' => $licId,
                'status' => [
                    'description' => 'unit_LicStatus',
                ],
                'licNo' => 'unit_LicNo',
            ],
        ];

        static::assertSame(
            '<a href="EXPECT_LIC_URL">unit_LicNo</a> (unit_LicStatus)',
            CaseEntityNrStatus::format($data, null, $this->mockSm)
        );
    }

    public function testFormatApp()
    {
        $licId = 9999;
        $appId = 8888;

        $this->mockUrlHlp
            ->shouldReceive('fromRoute')
            ->with('lva-licence', ['licence' => $licId])
            ->once()
            ->andReturn('EXPECT_LIC_URL')
            ->shouldReceive('fromRoute')
            ->with('lva-application', ['application' => $appId])
            ->once()
            ->andReturn('EXPECT_APP_URL');

        $data = [
            'caseType' => [
                'id' => \Common\RefData::CASE_TYPE_APPLICATION,
            ],
            'licence' => [
                'id' => $licId,
                'status' => [
                    'description' => 'unit_LicStatus',
                ],
                'licNo' => 'unit_LicNo',
            ],
            'application' => [
                'id' => $appId,
                'status' => [
                    'description' => 'unit_AppStatus',
                ],
            ],
        ];

        static::assertSame(
            '<a href="EXPECT_LIC_URL">unit_LicNo</a> (unit_LicStatus)' .
            '<br />/<a href="EXPECT_APP_URL">' . $appId . '</a> (unit_AppStatus)',
            CaseEntityNrStatus::format($data, null, $this->mockSm)
        );
    }
}
