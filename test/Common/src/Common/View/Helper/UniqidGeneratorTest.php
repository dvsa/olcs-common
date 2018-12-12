<?php

namespace CommonTest\View\Helper;

use Common\View\Helper\UniqidGenerator;
use Mockery\Adapter\Phpunit\MockeryTestCase;


class UniqidGeneratorTest extends MockeryTestCase
{

    public function testGetId()
    {
        $sut = new UniqidGenerator();
        $this->assertTrue(is_string($sut->getId()));
    }

    public function testRegenerateId()
    {
        $sut = new UniqidGenerator();
        $id = trim($sut->getId());
        $this->assertTrue(is_string($id) && !empty($id));
        $newId = trim($sut->regenerateId());
        $this->assertTrue(is_string($newId) && !empty($newId));
        $this->assertNotEquals($id, $newId);
    }
}