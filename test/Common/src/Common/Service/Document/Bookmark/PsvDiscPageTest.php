<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\PsvDiscPage;
use Common\Service\Document\Parser\RtfParser;

/**
 * Disc list test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class PsvDiscPageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new PsvDiscPage();
        $query = $bookmark->getQuery([123, 456]);

        $this->assertEquals(2, count($query));

        $this->assertEquals(123, $query[0]['data']['id']);
        $this->assertEquals(456, $query[1]['data']['id']);
    }

    public function testRenderWithNoData()
    {
        $parser = new RtfParser();
        $bookmark = new PsvDiscPage();
        $bookmark->setData([]);
        $bookmark->setParser($parser);

        $result = $bookmark->render();

        $this->assertEquals('', $result);
    }

    public function testRenderWithSixDiscsStillReturnsFullPage()
    {
        $data = [
            [
                'isCopy' => 'N',
                'discNo' => 1,
                'licence' => [
                    'organisation' => [
                        'name' => 'A short org name'
                    ],
                    'licNo' => 'L1234',
                    'inForceDate' => '2013-11-01',
                    'expiryDate' => '2014-10-03'
                ]
            ],
            /**
             * Data set 2: is a copy, long org name, no expiry date
             */
            [
                'isCopy' => 'Y',
                'discNo' => 2,
                'licence' => [
                    'organisation' => [
                        'name' => 'An extremely long org name which will split over multiple lines'
                    ],
                    'licNo' => 'L3143',
                    'inForceDate' => null,
                    'expiryDate' => null
                ]
            ]
        ];

        $parser = $this->getMock('Common\Service\Document\Parser\RtfParser', ['replace']);

        $expectedRowOne = [
            'PSV1_TITLE' => '',
            'PSV1_DISC_NO' => 1,
            'PSV1_LINE1' => 'A short org name',
            'PSV1_LINE2' => '',
            'PSV1_LINE3' => '',
            'PSV1_LICENCE' => 'L1234',
            'PSV1_VALID_DATE' => '2013-11-01',
            'PSV1_EXPIRY_DATE' => '2014-10-03',

            'PSV2_TITLE' => 'COPY',
            'PSV2_DISC_NO' => 2,
            'PSV2_LINE1' => 'An extremely long org n',
            'PSV2_LINE2' => 'ame which will split ov',
            'PSV2_LINE3' => 'er multiple lines',
            'PSV2_LICENCE' => 'L3143',
            'PSV2_VALID_DATE' => 'N/A',
            'PSV2_EXPIRY_DATE' => 'N/A',

            'PSV3_TITLE' => 'XXXXXXXXXX',
            'PSV3_DISC_NO' => 'XXXXXX',
            'PSV3_LINE1' => 'XXXXXXXXXX',
            'PSV3_LINE2' => 'XXXXXXXXXX',
            'PSV3_LINE3' => 'XXXXXXXXXX',
            'PSV3_LICENCE' => 'XXXXXXXXXX',
            'PSV3_VALID_DATE' => 'XXXXXXXXXX',
            'PSV3_EXPIRY_DATE' => 'XXXXXXXXXX',

            'PSV4_TITLE' => 'XXXXXXXXXX',
            'PSV4_DISC_NO' => 'XXXXXX',
            'PSV4_LINE1' => 'XXXXXXXXXX',
            'PSV4_LINE2' => 'XXXXXXXXXX',
            'PSV4_LINE3' => 'XXXXXXXXXX',
            'PSV4_LICENCE' => 'XXXXXXXXXX',
            'PSV4_VALID_DATE' => 'XXXXXXXXXX',
            'PSV4_EXPIRY_DATE' => 'XXXXXXXXXX',

            'PSV5_TITLE' => 'XXXXXXXXXX',
            'PSV5_DISC_NO' => 'XXXXXX',
            'PSV5_LINE1' => 'XXXXXXXXXX',
            'PSV5_LINE2' => 'XXXXXXXXXX',
            'PSV5_LINE3' => 'XXXXXXXXXX',
            'PSV5_LICENCE' => 'XXXXXXXXXX',
            'PSV5_VALID_DATE' => 'XXXXXXXXXX',
            'PSV5_EXPIRY_DATE' => 'XXXXXXXXXX',

            'PSV6_TITLE' => 'XXXXXXXXXX',
            'PSV6_DISC_NO' => 'XXXXXX',
            'PSV6_LINE1' => 'XXXXXXXXXX',
            'PSV6_LINE2' => 'XXXXXXXXXX',
            'PSV6_LINE3' => 'XXXXXXXXXX',
            'PSV6_LICENCE' => 'XXXXXXXXXX',
            'PSV6_VALID_DATE' => 'XXXXXXXXXX',
            'PSV6_EXPIRY_DATE' => 'XXXXXXXXXX'
        ];

        $parser->expects($this->at(0))
            ->method('replace')
            ->with('snippet', $expectedRowOne)
            ->willReturn('foo');

        $bookmark = $this->getMock('Common\Service\Document\Bookmark\PsvDiscPage', ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->willReturn('snippet');

        $bookmark->setData($data);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
        $this->assertEquals('foo', $result);
    }
}
