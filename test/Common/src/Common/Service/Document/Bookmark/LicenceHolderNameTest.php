<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\LicenceHolderName;

/**
 * Licence holder name test test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceHolderNameTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new LicenceHolderName();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRender()
    {
        $bookmark = new LicenceHolderName();
        $bookmark->setData(
            [
                'organisation' => [
                    'name' => 'Org 1'
                ]
            ]
        );

        $this->assertEquals(
            'Org 1',
            $bookmark->render()
        );
    }
}
