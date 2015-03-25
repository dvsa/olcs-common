<?php

namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\CompanyTradingName;

/**
 * Company trading name test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CompanyTradingNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new CompanyTradingName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithTradingNames()
    {
        $bookmark = new CompanyTradingName();
        $bookmark->setData(
            [
                'correspondenceCd' => [
                    'address' => [
                        'addressLine1' => 'Line 1',
                        'addressLine2' => 'Line 2',
                        'addressLine3' => 'Line 3',
                        'addressLine4' => 'Line 4',
                        'postcode' => 'LS1 1BC'
                    ]
                ],
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
        );
        $this->assertEquals(
            "An Org\nT/A TN 2\nLine 1\nLine 2\nLine 3\nLine 4\nLS1 1BC",
            $bookmark->render()
        );
    }

    public function testRenderWithNoTradingNames()
    {
        $bookmark = new CompanyTradingName();
        $bookmark->setData(
            [
                'correspondenceCd' => [
                    'address' => [
                        'addressLine1' => 'Line 1',
                        'addressLine2' => 'Line 2',
                        'addressLine3' => 'Line 3',
                        'addressLine4' => 'Line 4',
                        'postcode' => 'LS1 1BC'
                    ]
                ],
                'organisation' => [
                    'name' => 'An Org',
                    'tradingNames' => []
                ]
            ]
        );
        $this->assertEquals(
            "An Org\nLine 1\nLine 2\nLine 3\nLine 4\nLS1 1BC",
            $bookmark->render()
        );
    }
}
