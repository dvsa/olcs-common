<?php
/**
 * FormTest
 * 
 * @package CommonTest\Form\View\Helper
 * @author Jakub Igla <jakub.igla@gmail.com>
 */

namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form\View\Helper;

class FormTest extends \PHPUnit_Framework_TestCase
{

    protected $form;

    public function setUp()
    {
        $this->form = new \Zend\Form\Form('test');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderFormWithElement()
    {
        $this->form->add(new \Zend\Form\Element\Text('test'));

        $helpers = new HelperPluginManager();
        $helpers->setService('formRow', new Helper\FormRow());
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\Form();
        $viewHelper->setView($view);
        echo $viewHelper($this->form, 'form', '/');

        $this->expectOutputRegex('/^<form action="(.*)" method="(POST|GET)" name="test" id="test"><\/form>$/');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderFormWithFieldset()
    {
        $this->form->add(new \Zend\Form\Fieldset('test'));

        $helpers = new HelperPluginManager();
        $helpers->setService('formCollection', new Helper\FormCollection());
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\Form();
        $viewHelper->setView($view);
        echo $viewHelper($this->form, 'form', '/');

        $this->expectOutputRegex('/^<form action="(.*)" method="(POST|GET)" name="test" id="test"><\/form>$/');
    }

}
