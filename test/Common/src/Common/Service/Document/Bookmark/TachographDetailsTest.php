<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TachographDetails;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\LicenceEntityService;

/**
 * TachographDetails bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TachographDetailsTest extends MockeryTestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TachographDetails();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoTachographDetails()
    {
        $bookmark = new TachographDetails();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithTachographDetails()
    {
        $bookmark = m::mock('Common\Service\Document\Bookmark\TachographDetails')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('TachographDetails')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'tachographIns' => [
                    'id' => LicenceEntityService::LICENCE_TACH_INTERNAL
                ],
                'tachographInsName' => 'foo'
            ]
        );

        $content = [
            'Address' => 'foo',
            'checkbox1' => 'X',
            'checkbox2' => '',
            'checkbox3' => ''
        ];

        $mockParser = m::mock('Common\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $content)
            ->andReturn('content')
            ->once()
            ->getMock();

        $bookmark->setParser($mockParser);

        $this->assertEquals(
            'content',
            $bookmark->render()
        );
    }
}
