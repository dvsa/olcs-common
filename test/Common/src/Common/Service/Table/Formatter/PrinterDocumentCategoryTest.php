<?php

/**
 * PrinterDocumentCategory Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\PrinterDocumentCategory;

/**
 * PrinterDocumentCategory Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterDocumentCategoryTest extends MockeryTestCase
{
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

        $mockUrlHelper = m::mock()
            ->shouldReceive('fromRoute')
            ->with('admin-dashboard/admin-team-management', $params)
            ->once()
            ->andReturn('foo')
            ->getMock();

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper)
            ->once()
            ->getMock();

        $this->assertEquals($expected, PrinterDocumentCategory::format($data, [], $sm));
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
