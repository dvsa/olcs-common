<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\PiHearingVenue;

/**
 * Pi Hearing Venue test
 */
class PiHearingVenueTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new PiHearingVenue();

        $this->assertTrue(is_array($bookmark->getQuery(['hearing' => 123])));
        $this->assertTrue(is_null($bookmark->getQuery([])));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($data, $expected)
    {
        $bookmark = new PiHearingVenue();
        $bookmark->setData($data);

        $this->assertEquals($expected, $bookmark->render());
    }

    public function renderDataProvider()
    {
        return [
            [
                [
                    'piVenue' => ['name' => 'pi venue'],
                    'venueOther' => 'other venue'
                ],
                'pi venue'
            ],
            [
                [
                    'venueOther' => 'other venue'
                ],
                'other venue'
            ],
        ];
    }
}
