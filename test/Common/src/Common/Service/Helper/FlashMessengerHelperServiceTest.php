<?php

/**
 * Flash Messenger Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use PHPUnit_Framework_TestCase;
use Common\Service\Helper\FlashMessengerHelperService;

/**
 * Flash Messenger Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\FlashMessengerHelperService
     */
    private $sut;

    private $mockFlashMessenger;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->mockFlashMessenger = $this->getMock(
            '\Zend\Mvc\Controller\Plugin\FlashMessenger',
            array(
                'addSuccessMessage',
                'addErrorMessage',
                'addInfoMessage',
                'addWarningMessage'
            )
        );

        $mockServiceManager = \Mockery::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceManager->shouldReceive('get->get')->andReturn($this->mockFlashMessenger);

        $this->sut = new FlashMessengerHelperService();
        $this->sut->setServiceLocator($mockServiceManager);
    }

    /**
     * @group helper_service
     * @group flash_messenger_helper_service
     */
    public function testAddErrorMessage()
    {
        $message = 'foo';

        $this->mockFlashMessenger->expects($this->once())
            ->method('addErrorMessage')
            ->with($message)
            ->will($this->returnSelf());

        $this->assertSame($this->mockFlashMessenger, $this->sut->addErrorMessage($message));
    }

    /**
     * @group helper_service
     * @group flash_messenger_helper_service
     */
    public function testAddSuccessMessage()
    {
        $message = 'foo';

        $this->mockFlashMessenger->expects($this->once())
            ->method('addSuccessMessage')
            ->with($message)
            ->will($this->returnSelf());

        $this->assertSame($this->mockFlashMessenger, $this->sut->addSuccessMessage($message));
    }

    /**
     * @group helper_service
     * @group flash_messenger_helper_service
     */
    public function testAddWarningMessage()
    {
        $message = 'foo';

        $this->mockFlashMessenger->expects($this->once())
            ->method('addWarningMessage')
            ->with($message)
            ->will($this->returnSelf());

        $this->assertSame($this->mockFlashMessenger, $this->sut->addWarningMessage($message));
    }

    /**
     * @group helper_service
     * @group flash_messenger_helper_service
     */
    public function testAddInfoMessage()
    {
        $message = 'foo';

        $this->mockFlashMessenger->expects($this->once())
            ->method('addInfoMessage')
            ->with($message)
            ->will($this->returnSelf());

        $this->assertSame($this->mockFlashMessenger, $this->sut->addInfoMessage($message));
    }
}
