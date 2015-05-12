<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\TradingNames;

/**
 * Trading Names test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TradingNamesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new TradingNames();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoTradingNames()
    {
        $bookmark = new TradingNames();
        $bookmark->setData(
            [
                'organisation' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithTradingNames()
    {
        $bookmark = new TradingNames();
        $bookmark->setData(
            [
                'organisation' => [
                    'tradingNames' => [
                        'tn1',
                        'tn2',
                        'tn3'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'tn1, tn2, tn3',
            $bookmark->render()
        );
    }
}
