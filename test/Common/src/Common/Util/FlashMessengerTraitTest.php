<?php

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace CommonTest\Controller\Util;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class FlashMessengerTraitTest extends AbstractHttpControllerTestCase
{

    public function testGetFlashMessenger()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'getParams',
                'log',
                'plugin'
            )
        );
        $pluginManager = $this->getMock('\stdClass', array('getNamespace'));
        $this->trait->expects($this->once())
            ->method('plugin')
            ->will($this->returnValue($pluginManager));
        $this->trait->getFlashMessenger('backend\VosaCase');
    }

    public function testAddInfoMessage()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'getFlashMessenger',
                'log'
            )
        );
        $chainMock = $this->getMock('\stdClass', array('addInfoMessage'));
        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));
        $this->trait->addInfoMessage('backend\VosaCase');
    }

    public function testErrorMessage()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'getFlashMessenger',
                'log'
            )
        );
        $chainMock = $this->getMock('\stdClass', array('addErrorMessage'));
        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));
        $this->trait->addErrorMessage('backend\VosaCase');
    }

    public function testSuccessMessage()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'getFlashMessenger',
                'log'
            )
        );
        $chainMock = $this->getMock('\stdClass', array('addSuccessMessage'));
        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));
        $this->trait->addSuccessMessage('backend\VosaCase');
    }
}
