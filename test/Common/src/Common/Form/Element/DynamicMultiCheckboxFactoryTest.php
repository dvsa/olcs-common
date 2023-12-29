<?php

declare(strict_types=1);

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiCheckbox;
use Common\Form\Element\DynamicMultiCheckboxFactory;

class DynamicMultiCheckboxFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInvoke()
    {
        $mockSl = $this->createMock('\Laminas\Form\FormElementManager');
        $mockSl->expects($this->any())->method('get')->willReturnSelf();

        $sut = new DynamicMultiCheckboxFactory();
        $service = $sut->__invoke($mockSl, DynamicMultiCheckbox::class);

        $this->assertInstanceOf(DynamicMultiCheckbox::class, $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
