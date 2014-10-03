<?php

/**
 * Flash Messenger View Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Common\View\Helper\FlashMessenger;

/**
 * Flash Messenger View Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Subject under test
     *
     * @var \Common\View\Helper\FlashMessenger
     */
    private $sut;

    private $mockPluginManager;

    public function setUp()
    {
        $this->mockPluginManager = $this->getMock(
            '\Zend\Mvc\Controller\Plugin\FlashMessenger',
            array('getMessagesFromNamespace')
        );

        $mockTranslator = $this->getMock('\Zend\I18n\Translator\Translator', array('translate'));
        $mockTranslator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translate')));

        $this->sut = new FlashMessenger();
        $this->sut->setPluginFlashMessenger($this->mockPluginManager);
        $this->sut->setTranslator($mockTranslator);
    }

    /**
     * Mock translation
     *
     * @param string $message
     * @return string
     */
    public function translate($message)
    {
        return '*' . $message . '*';
    }

    /**
     * @group view_helper
     * @group flash_messenger_view_helper
     */
    public function testRenderWithoutMessages()
    {
        $this->mockPluginManager->expects($this->any())
            ->method('getMessagesFromNamespace')
            ->will($this->returnValue(array()));

        $markup = $this->sut->render();

        $this->assertEquals('', $markup);
    }

    /**
     * @group view_helper
     * @group flash_messenger_view_helper
     */
    public function testInvokeWithoutMessages()
    {
        $this->mockPluginManager->expects($this->any())
            ->method('getMessagesFromNamespace')
            ->will($this->returnValue(array()));

        $obj = $this->sut;

        $markup = $obj();

        $this->assertEquals('', $markup);
    }

    /**
     * @group view_helper
     * @group flash_messenger_view_helper
     */
    public function testRenderWithMessages()
    {
        // Return a message from each namespace
        $this->mockPluginManager->expects($this->any())
            ->method('getMessagesFromNamespace')
            ->will($this->returnValue(array('foo')));

        $expected = '<div class="notice-container">'
            . '<div  class="notice--danger"><p>*foo*</p></div>'
            . '<div  class="notice--success"><p>*foo*</p></div>'
            . '<div  class="notice--warning"><p>*foo*</p></div>'
            . '<div  class="notice--info"><p>*foo*</p></div>'
            . '<div  class="notice--info"><p>*foo*</p></div>'
            . '</div>';

        $markup = $this->sut->render();

        $this->assertEquals($expected, $markup);
    }
}
