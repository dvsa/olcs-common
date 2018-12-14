<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\UniqidGenerator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class UniqidGeneratorTest extends MockeryTestCase
{

    public function testGetLastId()
    {
        $sut = new UniqidGenerator();
        $id = $sut->generateId();
        $this->assertSame($id, $sut->getLastId());
    }

    public function testGenerateId()
    {
        $sut = new UniqidGenerator();
        $id = trim($sut->generateId());
        $this->assertTrue(is_string($id) && !empty($id));
        $secondId = trim($sut->generateId());
        $this->assertTrue(is_string($secondId) && !empty($secondId));
        $thirdId = trim($sut->generateId());
        $this->assertTrue(is_string($thirdId) && !empty($thirdId));
        $this->assertNotEquals($id, $secondId);
        $this->assertNotEquals($id, $thirdId);
        $this->assertNotEquals($secondId, $thirdId);
    }
}
