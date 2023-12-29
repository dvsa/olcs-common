<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicSelect;
use Common\Form\Element\DynamicSelectFactory;

/**
 * Class DynamicSelectFactoryTest
 * @package CommonTest\Form\Element
 */
class DynamicSelectFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInvoke()
    {
        $mockSl = $this->createMock('\Laminas\Form\FormElementManager');
        $mockSl->expects($this->any())->method('get')->willReturnSelf();

        $sut = new DynamicSelectFactory();
        $service = $sut->__invoke($mockSl, DynamicSelect::class);

        $this->assertInstanceOf(DynamicSelect::class, $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
