<?php

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Util;

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerTraitTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function setUp()
    {
        $this->sut = $this->getMockForTrait(
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
        $this->sut = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait', array(), '', true, true, true, array(
                'plugin'
            )
        );

        $pluginManager = $this->createPartialMock('\stdClass', array('getNamespace'));

        $this->sut->expects($this->once())
            ->method('plugin')
            ->will($this->returnValue($pluginManager));

        $this->sut->getFlashMessenger();
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddInfoMessage()
    {
        $message = 'foo';

        $chainMock = $this->createPartialMock('\stdClass', array('addInfoMessage'));
        $chainMock->expects($this->once())
            ->method('addInfoMessage')
            ->with($message);

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addInfoMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddErrorMessage()
    {
        $message = 'foo';

        $chainMock = $this->createPartialMock('\stdClass', array('addErrorMessage'));
        $chainMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($message);

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addErrorMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddSuccessMessage()
    {
        $message = 'foo';

        $chainMock = $this->createPartialMock('\stdClass', array('addSuccessMessage'));
        $chainMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with($message);

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addSuccessMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddWarningMessage()
    {
        $message = 'foo';

        $chainMock = $this->createPartialMock('\stdClass', array('addWarningMessage'));
        $chainMock->expects($this->once())
            ->method('addWarningMessage')
            ->with($message);

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addWarningMessage($message);
    }

    /**
     * @group util
     * @group flash_messenger_trait
     */
    public function testAddMessage()
    {
        $message = 'foo';
        $namespace = 'error';

        $chainMock = $this->createPartialMock('\stdClass', array('addMessage', 'setNamespace'));
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

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addMessage($message, $namespace);
    }
}
