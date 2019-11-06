<?php

/**
 * Publication Number Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\PublicationNumber;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Publication Number Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PublicationNumberTest extends MockeryTestCase
{
    /**
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expected)
    {
        $params = ['foo' => 'bar'];

        $config = [
            'windows_7_document_share' => [
                'uri_pattern' => '//foo/%s'
            ],
            'windows_10_document_share' => [
                'uri_pattern' => '//foo/%s'
            ]
        ];

        $pubService = m::mock();
        $pubService->shouldReceive('getFilePathVariablesFromPublication')
            ->with($data)
            ->andReturn($params);

        $sm = m::mock();
        $sm->shouldReceive('get')
            ->with('DataServiceManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('get')
                    ->with('Common\Service\Data\Publication')
                    ->andReturn($pubService)
                    ->getMock()
            )
            ->shouldReceive('get')
            ->with('Config')
            ->andReturn($config);

        $this->assertEquals($expected, PublicationNumber::format($data, $column, $sm));
    }

    public function provider()
    {
        return [
            [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_new'
                    ],
                    'publicationNo' => 12345
                ],
                [],
                12345
            ],
            [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_generated'
                    ],
                    'publicationNo' => 12345,
                    'document' => [
                        'identifier' => 'some/path/foo.rtf',
                        'id' => 987654,
                        'osType' =>'windows_7'
                    ]
                ],
                [],
                '<a href="//foo/some/path/foo.rtf" data-file-url="//foo/some/path/foo.rtf" target="blank">12345</a>'
            ],
            [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_something_else'
                    ],
                    'publicationNo' => 12345,
                    'document' => [
                        'identifier' => 'some/path/foo.rtf',
                        'id' => 987654,
                        'osType' =>'windows_10'
                    ]
                ],
                [],
                '<a href="/file/987654">'
                    . '12345</a>'
            ]
        ];
    }
}
