<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\SubjectAddress;

/**
 * Subject Address test
 */
class SubjectAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new SubjectAddress();

        $this->assertTrue(is_null($bookmark->getQuery([])));

        $query = $bookmark->getQuery(['opposition' => 123]);

        $this->assertEquals('Opposition', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new SubjectAddress();
        $bookmark->setData($data);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            // no results
            [
                [],
                ''
            ],
            // opposer with contact address
            [
                [
                    'opposer' => [
                        'contactDetails' => [
                            'address' => [
                                'addressLine1' => 'Line 1',
                                'addressLine2' => 'Line 2'
                            ]
                        ]
                    ]
                ],
                "Line 1\nLine 2"
            ],
        ];
    }
}
