<?php

/**
 * FormElementErrors Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * FormElementErrors Test
 *
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormElementErrors extends \PHPUnit_Framework_TestCase
{

    protected $element;

    public function setUp()
    {
        $this->element = new \Zend\Form\Element\Text('test');
        $this->element->setMessages(['Message']);
    }

    /**
     * @outputBuffering disabled
     */
    public function testRender()
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $translateHelper = new \Zend\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);

        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormElementErrors();
        $viewHelper->setView($view);
        echo $viewHelper($this->element);

        $this->expectOutputRegex('/^<ul><li>(.*)<\/li><\/ul>$/');
    }
}
