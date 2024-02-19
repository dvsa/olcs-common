<?php

/**
 * PrinterDocumentCategory Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Service\Table\Formatter\PrinterDocumentCategory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PrinterDocumentCategory Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterDocumentCategoryTest extends MockeryTestCase
{
    protected $urlHelper;
    protected $sut;

    protected function setUp(): void
    {
        $this->urlHelper = m::mock(UrlHelperService::class);
        $this->sut = new PrinterDocumentCategory($this->urlHelper);
    }

    /**
     * Test formatter
     *
     * @dataProvider provider
     * @param array $data
     * @param string $expected
     */
    public function testFormat($data, $expected)
    {
        $params = [
            'rule' => $data['id'],
            'action' => 'editRule',
            'team' => $data['team']['id']
        ];

        $this->urlHelper
            ->shouldReceive('fromRoute')
            ->with('admin-dashboard/admin-team-management', $params)
            ->once()
            ->andReturn('foo');

        $this->assertEquals($expected, $this->sut->format($data, []));
    }

    public function provider()
    {
        return [
            'with sub category' => [
                [
                    'id' => 1,
                    'team' => [
                        'id' => 2
                    ],
                    'subCategory' => [
                        'subCategoryName' => 'bar',
                        'category' => [
                            'description' => 'cake'
                        ]
                    ]
                ],
                '<a href="foo" class="govuk-link js-modal-ajax">cake / bar</a>'
            ],
            'defsult setting' => [
                [
                    'id' => 1,
                    'team' => [
                        'id' => 2
                    ],
                ],
                '<a href="foo" class="govuk-link js-modal-ajax">Default setting</a>'
            ]
        ];
    }
}
