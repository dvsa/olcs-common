<?php

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Util;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerTraitTest extends AbstractHttpControllerTestCase
{
    private $trait;

    public function setUp()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'getFlashMessenger'
            )
        );
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testGetFlashMessenger()
    {
        $this->trait = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'plugin'
            )
        );

        $pluginManager = $this->getMock('\stdClass', array('getNamespace'));

        $this->trait->expects($this->once())
            ->method('plugin')
            ->will($this->returnValue($pluginManager));

        $this->trait->getFlashMessenger();
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddInfoMessage()
    {
        $message = 'foo';

        $chainMock = $this->getMock('\stdClass', array('addInfoMessage'));
        $chainMock->expects($this->once())
            ->method('addInfoMessage')
            ->with($message);

        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->trait->addInfoMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddErrorMessage()
    {
        $message = 'foo';

        $chainMock = $this->getMock('\stdClass', array('addErrorMessage'));
        $chainMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($message);

        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->trait->addErrorMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddSuccessMessage()
    {
        $message = 'foo';

        $chainMock = $this->getMock('\stdClass', array('addSuccessMessage'));
        $chainMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with($message);

        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->trait->addSuccessMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddWarningMessage()
    {
        $message = 'foo';

        $chainMock = $this->getMock('\stdClass', array('addWarningMessage'));
        $chainMock->expects($this->once())
            ->method('addWarningMessage')
            ->with($message);

        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->trait->addWarningMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddMessage()
    {
        $message = 'foo';
        $namespace = 'error';

        $chainMock = $this->getMock('\stdClass', array('addMessage', 'setNamespace'));
        $chainMock->expects($this->at(0))
            ->method('setNamespace')
            ->with($namespace)
            ->will($this->returnSelf());
        $chainMock->expects($this->at(1))
            ->method('addMessage')
            ->with($message)
            ->will($this->returnSelf());
        $chainMock->expects($this->at(2))
            ->method('setNamespace')
            ->with('default')
            ->will($this->returnSelf());

        $this->trait->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->trait->addMessage($message, $namespace);
    }
}
