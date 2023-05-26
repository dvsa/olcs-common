<?php

/**
 * LicenceNumberLinkTest.php
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\LicenceNumberLink;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class LicenceNumberLinkTest
 *
 * @package CommonTest\Service\Table\Formatter
 */
class LicenceNumberLinkTest extends TestCase
{
    protected $urlHelper;
    protected $sut;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->sut = new LicenceNumberLink($this->urlHelper);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @dataProvider formatProvider
     */
    public function testFormat($data, $expected)
    {
        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with(
                'lva-licence',
                [
                    'licence' => $data['licence']['id']
                ]
            )
            ->andReturn('LICENCE_URL');

        $this->assertEquals($expected, $this->sut->format($data, []));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'licence' => [
                        'id' => 1,
                        'licNo' => 0001,
                        'status' => 'lsts_valid'
                    ]
                ],
                '<a class="govuk-link" href="LICENCE_URL">1</a>'
            ],
            [
                [
                    'licence' => [
                        'id' => 1,
                        'licNo' => 0001,
                        'status' => 'not-valid'
                    ]
                ],
                '1'
            ]
        ];
    }
}
