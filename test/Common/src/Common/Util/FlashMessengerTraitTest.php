<?php

namespace CommonTest\Controller\Util;

use Laminas\Mvc\Controller\Plugin\FlashMessenger as FlashMessengerPlugin;
use Mockery as m;

/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerTraitTest extends m\Adapter\Phpunit\MockeryTestCase
{
    private $sut;

    public function setUp(): void
    {
        $this->sut = $this->getMockForTrait(
            '\Common\Util\FlashMessengerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
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
            '\Common\Util\FlashMessengerTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'plugin'
            )
        );

        $pluginManager = $this->createPartialMock(FlashMessengerPlugin::class, array('getNamespace'));

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

        $chainMock = $this->createPartialMock(FlashMessengerPlugin::class, array('addInfoMessage'));
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

        $chainMock = $this->createPartialMock(FlashMessengerPlugin::class, array('addErrorMessage'));
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

        $chainMock = $this->createPartialMock(FlashMessengerPlugin::class, array('addSuccessMessage'));
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

        $chainMock = $this->createPartialMock(FlashMessengerPlugin::class, array('addWarningMessage'));
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

        $chainMock = m::mock(FlashMessengerPlugin::class);
        $chainMock->expects('setNamespace')->with($namespace)->andReturnSelf();
        $chainMock->expects('addMessage')->with($message)->andReturnSelf();
        $chainMock->expects('setNamespace')->with('default')->andReturnSelf();

        $this->sut->expects($this->once())
            ->method('getFlashMessenger')
            ->will($this->returnValue($chainMock));

        $this->sut->addMessage($message, $namespace);
    }
}
