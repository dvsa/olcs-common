<?php

/**
 * Flash messenger view helper (Extends zends flash messenger)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\View\Helper\FlashMessenger as ZendFlashMessenger;
use Zend\Mvc\Controller\Plugin\FlashMessenger as PluginFlashMessenger;

/**
 * Flash messenger view helper (Extends zends flash messenger)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessenger extends ZendFlashMessenger
{
    /**
     * Templates for the open/close/separators for message tags
     *
     * @var string
     */
    protected $messageCloseString     = '</p></div>';
    protected $messageOpenFormat      = '<div %s><p>';
    protected $messageSeparatorString = '</p></div><div %s><p>';

    /**
     * Holds the wrapper format
     *
     * @var string
     */
    private $wrapper = '<div class="notice-container">%s</div>';

    public function __invoke($namespace = null)
    {
        return $this->render();
    }

    /**
     * Render Messages
     *
     * @param  string $namespace
     * @param  array  $classes
     * @return string
     */
    public function render($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $classes = array())
    {
        $markup = $this->renderAllFromNamespace('error', array('notice--danger'));
        $markup .= $this->renderAllFromNamespace('success', array('notice--success'));
        $markup .= $this->renderAllFromNamespace('warning', array('notice--warning'));
        $markup .= $this->renderAllFromNamespace('info', array('notice--info'));
        $markup .= $this->renderAllFromNamespace('default', array('notice--info'));

        if (empty($markup)) {
            return '';
        }

        return sprintf($this->wrapper, $markup);
    }

    /**
     * Render all from namespace
     *
     * @param string $namespace
     * @param array $classes
     * @return string
     */
    protected function renderAllFromNamespace($namespace, $classes)
    {
        return parent::render($namespace, $classes) . $this->renderCurrent($namespace, $classes);
    }

    /**
     * Majority of this is copied from Zend's however I have removed the code to escape html, as we need to display HTML
     *  in our flash messengers, and we shouldn't ever need to escape it as our messages will never contain user entered
     *  info
     *
     * @param  array $messages
     * @param  array $classes
     * @return string
     */
    protected function renderMessages($namespace = PluginFlashMessenger::NAMESPACE_DEFAULT, array $messages = array(), array $classes = array())
    {
        // Prepare classes for opening tag
        if (empty($classes)) {
            if (isset($this->classMessages[$namespace])) {
                $classes = $this->classMessages[$namespace];
            } else {
                $classes = $this->classMessages[PluginFlashMessenger::NAMESPACE_DEFAULT];
            }
            $classes = array($classes);
        }
        // Flatten message array
        $messagesToPrint = array();
        $translator = $this->getTranslator();
        $translatorTextDomain = $this->getTranslatorTextDomain();
        array_walk_recursive($messages, function ($item) use (&$messagesToPrint, $translator, $translatorTextDomain) {
            if ($translator !== null) {
                $item = $translator->translate(
                    $item,
                    $translatorTextDomain
                );
            }
            $messagesToPrint[] = $item;
        });
        if (empty($messagesToPrint)) {
            return '';
        }
        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), ' class="' . implode(' ', $classes) . '"');
        $markup .= implode(sprintf($this->getMessageSeparatorString(), ' class="' . implode(' ', $classes) . '"'), $messagesToPrint);
        $markup .= $this->getMessageCloseString();
        return $markup;
    }
}
