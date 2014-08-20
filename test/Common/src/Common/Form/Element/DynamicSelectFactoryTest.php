<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicSelectFactory;

/**
 * Class DynamicSelectFactoryTest
 * @package CommonTest\Form\Element
 */
class DynamicSelectFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $mockRefDataService = $this->getMock('\Common\Service\RefData');

        $mockSl = $this->getMock('\Zend\Form\FormElementManager');
        $mockSl->expects($this->any())->method('getServiceLocator')->willReturnSelf();
        $mockSl->expects($this->once())->method('get')->willReturn($mockRefDataService);

        $sut = new DynamicSelectFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Form\Element\DynamicSelect', $service);
        $this->assertSame($mockRefDataService, $service->getRefDataService());
    }
}
