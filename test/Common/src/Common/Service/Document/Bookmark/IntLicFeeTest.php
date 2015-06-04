<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\IntLicFee;

/**
 * Interim Licence Fee bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class IntLicFeeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new IntLicFee();
        $query = $bookmark->getQuery(['fee' => 123]);

        $this->assertEquals('Fee', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoIntLicFee()
    {
        $bookmark = new IntLicFee();
        $bookmark->setData(
            [
                'amount' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithIntLicFee()
    {
        $bookmark = new IntLicFee();
        $bookmark->setData(
            [
                'amount' => '123456'
            ]
        );

        $this->assertEquals(
            '123,456',
            $bookmark->render()
        );
    }
}
