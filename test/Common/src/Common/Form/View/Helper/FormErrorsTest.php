<?php

/**
 * FormErrors Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
namespace CommonTest\Form\View\Helper;

use Zend\View\HelperPluginManager;
use Zend\View\Renderer\PhpRenderer;
use Zend\Form;

/**
 * FormErrors Test
 *
 * @author Jakub Igla <jakub.igla@gmail.com>
 */
class FormErrors extends \PHPUnit_Framework_TestCase
{
    private function prepareForm($valid = false)
    {
        $form = new Form\Form();

        $element = new \Common\Form\Elements\InputFilters\TextRequired('test');
        $element->setOptions([
            'label' => 'Label',
        ]);
        $form->add($element);

        $form->setData(
            $valid
            ? ['test' => 'test']
            : ['test' => '']
        );

        $form->isValid();

        return $form;
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithNoForm()
    {
        $helpers = new HelperPluginManager();
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormErrors();
        $viewHelper->setView($view);
        $viewHelper();

        $this->expectOutputString('');
    }


    /**
     * @outputBuffering disabled
     */
    public function testRenderWithNoValid()
    {
        $helpers = new HelperPluginManager();
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormErrors();
        $viewHelper->setView($view);
        echo $viewHelper($this->prepareForm(true));

        $this->expectOutputString('');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithId()
    {
        $viewHelper = $this->prepareHelper();
        $form = $this->prepareForm();
        $form->get('test')->setAttribute('id', 'test');

        echo $viewHelper($form);

        $this->expectOutputRegex('/^<div class="validation-summary">(.*)<a href="#(.*)">(.*)<\/a>(.*)<\/div>$/s');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRender()
    {
        $viewHelper = $this->prepareHelper();
        $form = $this->prepareForm();

        echo $viewHelper($form);

        $this->expectOutputRegex('/^<div class="validation-summary">(.*)<\/div>$/s');
    }

    /**
     * @outputBuffering disabled
     */
    public function testRenderWithMoreMessages()
    {
        $viewHelper = $this->prepareHelper();
        $form = $this->prepareForm();

        $form->get('test')->setMessages([['Msg']]);

        echo $viewHelper($form);

        $this->expectOutputRegex('/^<div class="validation-summary">(.*)<\/div>$/s');
    }

    private function prepareHelper()
    {
        $translator = new \Zend\I18n\Translator\Translator();
        $translateHelper = new \Zend\I18n\View\Helper\Translate();
        $translateHelper->setTranslator($translator);

        $helpers = new HelperPluginManager();
        $helpers->setService('translate', $translateHelper);
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new \Common\Form\View\Helper\FormErrors();
        $viewHelper->setView($view);

        return $viewHelper;
    }
}
