<?php


namespace CommonTest\Service;

use Common\Service\BusRegistration;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class BusRegistrationTest
 * @package CommonTest\Service
 */
class BusRegistrationTest extends TestCase
{
    public function testCreateNew()
    {
        $licence = ['id' => 123, 'licNo' => 'AB12563'];

        $sut = new BusRegistration();
        $result = $sut->createNew($licence);

        $this->assertInternalType('array', $result);
        $this->assertSame($licence['id'], $result['licence']['id']);

        $this->assertEquals(BusRegistration::STATUS_NEW, $result['status']);
        $this->assertEquals(BusRegistration::STATUS_NEW, $result['revertStatus']);

        $this->assertArrayHasKey('shortNotices', $result);
        $this->assertNotEmpty($result['shortNotices']);

        return $result;
    }

    /**
     * @depends testCreateNew
     * @param $new
     * @return array
     */
    public function testCreateVariation($new)
    {
        $new['otherServices'][] = ['id' => 45, 'busRegId' => 17, 'serviceNo' => '4a'];
        $new['trcConditionChecked'] = 'Y';
        $new['id'] = 17;

        $sut = new BusRegistration();
        $result = $sut->createVariation($new);

        $this->assertEquals(1, $result['variationNo']);
        $this->assertEquals(BusRegistration::STATUS_VAR, $result['status']);
        $this->assertEquals(BusRegistration::STATUS_VAR, $result['revertStatus']);

        $this->assertSame($new['id'], $result['parent']['id']);

        $this->assertEquals('N', $result['trcConditionChecked']);
        $this->assertEquals(['serviceNo' => '4a'], $result['otherServices'][0]);

        $this->assertArrayNotHasKey('id', $result);

        return $result;
    }

    /**
     * @depends testCreateVariation
     * @param $variation
     */
    public function testCreateCancellation($variation)
    {
        $variation['id'] = 18;
        $sut = new BusRegistration();
        $result = $sut->createCancellation($variation);

        $this->assertEquals(2, $result['variationNo']);
        $this->assertEquals(BusRegistration::STATUS_CANCEL, $result['status']);
        $this->assertEquals(BusRegistration::STATUS_CANCEL, $result['revertStatus']);
    }

    public function testGetCascadeOptions()
    {
        $sut = new BusRegistration();
        $options = $sut->getCascadeOptions();

        $this->assertInternalType('array', $options);
        $this->assertArrayHasKey('cascade', $options);
        $this->assertArrayHasKey('list', $options['cascade']);
        $this->assertNotEmpty($options['cascade']['list']);
    }

}
