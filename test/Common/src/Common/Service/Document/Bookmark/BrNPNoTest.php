<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\BrNPNo;

/**
 * Br N P No test
 */
class BrNPNoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new BrNPNo();

        $this->assertTrue(is_array($bookmark->getQuery(['busRegId' => 123])));
        $this->assertTrue(is_null($bookmark->getQuery([])));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new BrNPNo();
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
            // results without publication
            [
                [
                    'Results' => [
                        ['id' => 1]
                    ]
                ],
                ''
            ],
            // results with publication
            [
                [
                    'Results' => [
                        ['id' => 1, 'publication' => ['publicationNo' => 10]],
                        ['id' => 1, 'publication' => ['publicationNo' => 11]],
                        ['id' => 1, 'publication' => ['publicationNo' => 12]],
                    ]
                ],
                12
            ],
        ];
    }
}
