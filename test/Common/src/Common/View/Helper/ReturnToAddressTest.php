<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\ReturnToAddress;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\View\Helper\ReturnToAddress
 */
class ReturnToAddressTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestInvoke
     */
    public function testInvoke($isNi, $separator, $expect): void
    {
        $sut = new ReturnToAddress();

        if ($separator !== null) {
            $actual = $sut($isNi, $separator);
            $actualStatic = $sut::getAddress($isNi, $separator);
        } else {
            $actual = $sut($isNi);
            $actualStatic = $sut::getAddress($isNi);
        }

        static::assertEquals($expect, $actual);
        static::assertEquals($actual, $actualStatic);
    }

    public function dpTestInvoke()
    {
        return [
            [
                'isNi' => false,
                'separator' => null,
                'expect' => 'Office of the Traffic Commissioner, The Central Licensing Office, Hillcrest House, ' .
                    '386 Harehills Lane, Leeds, LS9 6NF',
            ],
            [
                'isNi' => false,
                'separator' => '</br>',
                'expect' => 'Office of the Traffic Commissioner</br>The Central Licensing Office</br>Hillcrest House</br>' .
                    '386 Harehills Lane</br>Leeds</br>LS9 6NF',
            ],
            [
                'isNi' => true,
                'separator' => null,
                'expect' => 'Department for Infrastructure, The Central Licensing Office, PO Box 180, Leeds, LS9 1BU',
            ],
            [
                'isNi' => true,
                'separator' => '<br />',
                'expect' => 'Department for Infrastructure<br />' .
                    'The Central Licensing Office<br />PO Box 180<br />Leeds<br />LS9 1BU',
            ],
        ];
    }
}
