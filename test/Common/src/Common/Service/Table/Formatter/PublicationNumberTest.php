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
            );

        $this->assertEquals($expected, PublicationNumber::format($data, $column, $sm));
    }

    public function provider()
    {
        return [
            "no-document-type" => [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_new'
                    ],
                    'publicationNo' => 12345
                ],
                [],
                12345
            ],
            "document-webdav-generated" => [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_generated'
                    ],
                    'publicationNo' => 12345,
                    'document' => [
                        'identifier' => 'some/path/foo.rtf',
                        'id' => 987654,

                    ],
                    'webDavUrl' => 'ms-word:ofe|u|https://testhost/documents-dav/JWT/olcs/ID'
                ],
                [],
                '<a class="govuk-link" href="ms-word:ofe|u|https://testhost/documents-dav/JWT/olcs/ID" data-file-url="ms-word:ofe|u|https://testhost/documents-dav/JWT/olcs/ID" target="blank">12345</a>'
            ],
            "docman-config" => [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_something_else'
                    ],
                    'publicationNo' => 12345,
                    'document' => [
                        'identifier' => 'some/path/foo.rtf',
                        'id' => 987654
                    ],
                ],
                [],
                '<a class="govuk-link" href="/file/987654">'
                . '12345</a>'
            ]
        ];
    }
}
