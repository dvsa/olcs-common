<?php

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
namespace CommonTest\Controller\Util;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
class LoggerTraitTest extends AbstractHttpControllerTestCase
{

    public function testGetLogger()
    {
        $trait = $this->getMockForTrait(
            '\Common\Util\LoggerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'getServiceLocator',
                'setLogger'
            )
        );
        $serviceLocator= $this->getMock('\stdClass', array('get'));
        $logger = new \Zend\Log\Logger;
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValue($logger));
        $trait->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));
        $trait->expects($this->once())
            ->method('setLogger')
            ->with($logger);
        $trait->getLogger();
    }

    /**
     * @expectedException LogicException
     */
    public function testGetInvalidLogger()
    {
        $trait = $this->getMockForTrait(
            '\Common\Util\LoggerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'getServiceLocator',
                'setLogger'
            )
        );
        $serviceLocator= $this->getMock('\stdClass', array('get'));
        $logger = new \StdClass; // set logger to raise exception
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValue($logger));
        $trait->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));

        $trait->getLogger();
    }

    public function testLog()
    {
        $trait = $this->getMockForTrait(
            '\Common\Util\LoggerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'getLogger'
            )
        );

        $logger= $this->getMock('\stdClass', array('log'));

        $trait->expects($this->once())
            ->method('getLogger')
            ->will($this->returnValue($logger));
        $trait->log('a message', 'more info', array());
    }
}
