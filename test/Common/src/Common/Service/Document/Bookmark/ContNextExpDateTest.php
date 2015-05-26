<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\ContNextExpDate;

/**
 * Continuation Next Expiry Date test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ContNextExpDateTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new ContNextExpDate();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoExpiryDate()
    {
        $bookmark = new ContNextExpDate();
        $bookmark->setData(
            [
                'expiryDate' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithContNextExpirtyDate()
    {
        $bookmark = new ContNextExpDate();
        $bookmark->setData(
            [
                'expiryDate' => '2014-01-01'
            ]
        );

        $this->assertEquals(
            '01/01/2019',
            $bookmark->render()
        );
    }
}
