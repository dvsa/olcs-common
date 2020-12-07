<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiCheckboxFactory;

/**
 * Class DynamicMultiCheckboxFactoryTest
 * @package CommonTest\Form\Element
 */
class DynamicMultiCheckboxFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {

        $mockSl = $this->createMock('\Laminas\Form\FormElementManager');
        $mockSl->expects($this->any())->method('getServiceLocator')->willReturnSelf();
        $mockSl->expects($this->any())->method('get')->willReturnSelf();

        $sut = new DynamicMultiCheckboxFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Form\Element\DynamicMultiCheckbox', $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
