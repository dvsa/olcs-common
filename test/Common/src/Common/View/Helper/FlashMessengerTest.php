<?php

/**
 * Flash Messenger View Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\View\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\View\Helper\FlashMessenger;
use Mockery as m;

/**
 * Flash Messenger View Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessengerTest extends MockeryTestCase
{
    /**
     * Subject under test
     *
     * @var \Common\View\Helper\FlashMessenger
     */
    private $sut;

    private $sm;

    private $mockPluginManager;

    public function setUp()
    {
        $this->mockPluginManager = m::mock('\Zend\Mvc\Controller\Plugin\FlashMessenger');
        $this->sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');

        $mockTranslator = m::mock('\Zend\I18n\Translator\Translator');
        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(array($this, 'translate'));

        $this->sut = new FlashMessenger();
        $this->sut->setPluginFlashMessenger($this->mockPluginManager);
        $this->sut->setTranslator($mockTranslator);
        $this->sut->setServiceLocator($this->sm);
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
        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('getCurrentMessages')
            ->andReturn([]);

        $this->sm->shouldReceive('getServiceLocator')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('Helper\FlashMessenger')
            ->andReturn($mockFlashMessenger);

        $this->mockPluginManager->shouldReceive('getMessagesFromNamespace')
            ->andReturn([])
            ->shouldReceive('getCurrentMessagesFromNamespace')
            ->andReturn([]);

        $markup = $this->sut->render();

        $this->assertEquals('', $markup);
    }

    /**
     * @group view_helper
     * @group flash_messenger_view_helper
     */
    public function testInvokeWithoutMessages()
    {
        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('getCurrentMessages')
            ->andReturn([]);

        $this->sm->shouldReceive('getServiceLocator')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('Helper\FlashMessenger')
            ->andReturn($mockFlashMessenger);

        $this->mockPluginManager->shouldReceive('getMessagesFromNamespace')
            ->andReturn([])
            ->shouldReceive('getCurrentMessagesFromNamespace')
            ->andReturn([]);

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
        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('getCurrentMessages')
            ->andReturn(['foo']);

        $this->sm->shouldReceive('getServiceLocator')
            ->andReturnSelf()
            ->shouldReceive('get')
            ->with('Helper\FlashMessenger')
            ->andReturn($mockFlashMessenger);

        $this->mockPluginManager->shouldReceive('getMessagesFromNamespace')
            ->andReturn(['bar'])
            ->shouldReceive('getCurrentMessagesFromNamespace')
            ->andReturn(['baz']);

        $expected = '<div class="notice-container">'
            . '<div class="notice--danger"><p>*bar*</p></div>'
            . '<div class="notice--danger"><p>*baz*</p></div>'
            . '<div class="notice--danger"><p>*foo*</p></div>'
            . '<div class="notice--success"><p>*bar*</p></div>'
            . '<div class="notice--success"><p>*baz*</p></div>'
            . '<div class="notice--success"><p>*foo*</p></div>'
            . '<div class="notice--warning"><p>*bar*</p></div>'
            . '<div class="notice--warning"><p>*baz*</p></div>'
            . '<div class="notice--warning"><p>*foo*</p></div>'
            . '<div class="notice--info"><p>*bar*</p></div>'
            . '<div class="notice--info"><p>*baz*</p></div>'
            . '<div class="notice--info"><p>*foo*</p></div>'
            . '<div class="notice--info"><p>*bar*</p></div>'
            . '<div class="notice--info"><p>*baz*</p></div>'
            . '<div class="notice--info"><p>*foo*</p></div>'
            . '</div>';

        $markup = $this->sut->render();

        //check initial markup
        $this->assertEquals($expected, $markup);

        //check messages don't render twice
        $this->assertEquals('', $this->sut->render());
    }
}
