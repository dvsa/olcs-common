<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicSelectFactory;

/**
 * Class DynamicSelectFactoryTest
 * @package CommonTest\Form\Element
 */
class DynamicSelectFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateService()
    {

        $mockSl = $this->createMock('\Laminas\Form\FormElementManager');
        $mockSl->expects($this->any())->method('getServiceLocator')->willReturnSelf();
        $mockSl->expects($this->any())->method('get')->willReturnSelf();

        $sut = new DynamicSelectFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Form\Element\DynamicSelect', $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
