<?php

namespace CommonTest\Data\Object;

use Common\Data\Object\Bundle;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class BundleTest
 * @package CommonTest\Data\Object
 */
class BundleTest extends TestCase
{
    /**
     * @dataProvider provideJsonEncode
     * @param $object
     * @param $array
     */
    public function testJsonEncode($object, $array)
    {
        $this->assertEquals(json_encode($array), (string) $object);
    }

    public function provideJsonEncode()
    {
        $bundle1 = new Bundle();
        $bundle1->addChild('test');

        $subBundle2 = new Bundle();
        $subBundle2->addChild('foo');
        $bundle2 = new Bundle();
        $bundle2->addChild('test', $subBundle2);

        return [
            [new Bundle(), []],
            [$bundle1, ['children' => ['test']]],
            [$bundle2, ['children' => ['test' => ['children' => ['foo']]]]],
        ];
    }

    public function testAddChild()
    {
        $sut = new Bundle();
        $sut->addChild('test');

        $test = new Bundle();
        $sut->addChild('test1', $test);

        $this->assertEquals(['children' => ['test', 'test1' => $test]], $sut->jsonSerialize());
    }

    public function testAddCriteria()
    {
        $sut = new Bundle();
        $sut->addCriteria('test', 'value');
        $sut->addCriteria(['test2' => 'value', 'test2' => 'othervalue']);

        $this->assertEquals(
            ['criteria' => ['test' => 'value', ['test2' => 'value', 'test2' => 'othervalue']]],
            $sut->jsonSerialize()
        );
    }
}
