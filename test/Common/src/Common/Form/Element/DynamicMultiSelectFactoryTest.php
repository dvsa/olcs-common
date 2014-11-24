<?php

/**
 * Dynamic Multi Select Factory tests
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiSelectFactory;

/**
 * Dynamic Multi Select Factory tests
 *
 * @package CommonTest\Form\Element
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DynamicMultiSelectFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test create service
     * @group dynamicMultiSelectFactory
     */
    public function testCreateService()
    {
        $mockSl = $this->getMock('\Zend\Form\FormElementManager');
        $mockSl->expects($this->any())->method('getServiceLocator')->willReturnSelf();
        $mockSl->expects($this->any())->method('get')->willReturnSelf();

        $sut = new DynamicMultiSelectFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Form\Element\DynamicMultiSelect', $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
