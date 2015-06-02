<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\AddressOfEstablishment;

/**
 * Address of Establishment test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AddressOfEstablishmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new AddressOfEstablishment();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoAddressOfEstablishment()
    {
        $bookmark = new AddressOfEstablishment();
        $bookmark->setData(
            [
                'establishmentCd' => null
            ]
        );

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithAddressOfEstablishment()
    {
        $bookmark = new AddressOfEstablishment();
        $bookmark->setData(
            [
                'establishmentCd' => [
                    'address' => [
                        'addressLine1' => 'Line 1'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            'Line 1',
            $bookmark->render()
        );
    }
}
