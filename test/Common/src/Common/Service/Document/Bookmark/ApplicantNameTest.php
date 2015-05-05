<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\ApplicantName;

/**
 * Applicant name test
 */
class ApplicantNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new ApplicantName();

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
        $bookmark = new ApplicantName();
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
            // organisation without any trading names
            [
                [
                    'licence' => [
                        'organisation' => [
                            'name' => 'An Org',
                            'tradingNames' => []
                        ]
                    ]
                ],
                'An Org'
            ],
            // organisation with single trading name
            [
                [
                    'licence' => [
                        'organisation' => [
                            'name' => 'An Org',
                            'tradingNames' => [
                                [
                                    'name' => 'TN 1',
                                    'createdOn' => '2015-04-01 11:00:00'
                                ]
                            ]
                        ]
                    ]
                ],
                'An Org T/A TN 1'
            ],
            // organisation with multiple trading names
            [
                [
                    'licence' => [
                        'organisation' => [
                            'name' => 'An Org',
                            'tradingNames' => [
                                [
                                    'name' => 'TN 1',
                                    'createdOn' => '2015-04-01 11:00:00'
                                ],
                                [
                                    'name' => 'TN 2',
                                    'createdOn' => '2014-04-01 11:00:00'
                                ]
                            ]
                        ]
                    ]
                ],
                'An Org T/A TN 2'
            ],
        ];
    }
}
