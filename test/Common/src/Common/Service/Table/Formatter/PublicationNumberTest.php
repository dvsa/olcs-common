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
    public function testFormat($data, $expected)
    {
        $this->assertEquals($expected, PublicationNumber::format($data));
    }

    public function provider()
    {
        return [
            [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_foo_status'
                    ],
                    'publicationNo' => 12345
                ],
                12345
            ],
            [
                [
                    'pubStatus' => [
                        'id' => 'pub_s_printed'
                    ],
                    'publicationNo' => 12345,
                    'document' => [
                        'identifier' => 'some/path/foo.rtf',
                        'id' => 987654
                    ]
                ],
                '<a href="/file/987654">'
                    . '12345</a>'
            ]
        ];
    }
}
