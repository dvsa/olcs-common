<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\VehicleRow;
use Common\Service\Document\Parser\RtfParser;

/**
 * Vehicle Row test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VehicleRowTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new VehicleRow();
        $query = $bookmark->getQuery(['licence' => 7]);

        $this->assertEquals(7, $query['data']['id']);
        $this->assertEquals('Licence', $query['service']);
    }

    public function testRenderWithNoData()
    {
        $parser = new RtfParser();
        $bookmark = new VehicleRow();
        $bookmark->setData([]);
        $bookmark->setParser($parser);

        $result = $bookmark->render();

        $this->assertEquals('', $result);
    }

    public function testRenderLicenceVehicles()
    {
        $data = [
            'licenceVehicles' => [
                [
                    'specifiedDate' => '2014-07-03',
                    'removalDate' => null,
                    'vehicle' => [
                        'platedWeight' => 12345,
                        'vrm' => 'VRM123'
                    ]
                ], [
                    'specifiedDate' => '2014-07-03',
                    'removalDate' => '2014-10-10',
                    'vehicle' => [
                        'platedWeight' => 12345,
                        'vrm' => 'VRM321'
                    ]
                ], [
                    'specifiedDate' => null,
                    'removalDate' => null,
                    'vehicle' => [
                        'platedWeight' => 23456,
                        'vrm' => 'VRM456'
                    ]
                ]
            ]
        ];

        $parser = $this->getMock('Common\Service\Document\Parser\RtfParser', ['replace']);

        // note how we don't expect the second row to feature as it has been
        // marked as removed
        // similarly, the third row has no specified date so should also be omitted
        $expectedRowOne = [
            'SPEC_DATE' => '03-Jul-2014',
            'PLATED_WEIGHT' => 12345,
            'REG_MARK' => 'VRM123'
        ];

        $parser->expects($this->once())
            ->method('replace')
            ->with('snippet', $expectedRowOne)
            ->willReturn('foo');

        $bookmark = $this->getMock('Common\Service\Document\Bookmark\VehicleRow', ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->willReturn('snippet');

        $bookmark->setData($data);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
        $this->assertEquals('foo', $result);
    }
}
