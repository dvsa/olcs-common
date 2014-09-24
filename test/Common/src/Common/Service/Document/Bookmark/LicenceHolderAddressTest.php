<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\LicenceHolderAddress;

/**
 * Licence holder address test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceHolderAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new LicenceHolderAddress();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoCorrespondenceAddress()
    {
        $bookmark = new LicenceHolderAddress();
        $bookmark->setData(
            [
                'organisation' => [
                    'contactDetails' => [
                        [
                            'contactType' => [
                                'id' => 'foo'
                            ],
                            'address' => [
                                'addressLine1' => 'Line 1'
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->assertEquals(
            null,
            $bookmark->render()
        );
    }

    public function testRenderWithCorrespondenceAddress()
    {
        $bookmark = new LicenceHolderAddress();
        $bookmark->setData(
            [
                'organisation' => [
                    'contactDetails' => [
                        [
                            'contactType' => [
                                'id' => 'ct_corr'
                            ],
                            'address' => [
                                'addressLine1' => 'Line 1'
                            ]
                        ]
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
