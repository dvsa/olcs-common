<?php

namespace CommonTest\Data\Object;

use Common\Data\Object\Bundle\TransportManager;
use CommonTest\Bootstrap;

/**
 * Class TransportManagerBundleTest
 * @package CommonTest\Data\Object
 */
class TransportManagerBundleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group transportManagerBundle
     */
    public function testJsonEncode()
    {
        $sut = new TransportManager();
        $sut->init(Bootstrap::getServiceManager());
        $bundle = [
            'children' => [
                'homeCd' => [
                    'children' => [
                        'person',
                        'address'
                    ]
                ],
                'workCd' => [
                    'children' => [
                        'address'
                    ]
                ],
                'tmStatus',
                'tmType'
            ]
        ];
        $this->assertEquals(json_encode($bundle), (string) $sut->__toString());
    }
}
