<?php

/**
 * Flash messenger view helper (Extends zends flash messenger)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Flash messenger view helper (Extends zends flash messenger)
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FlashMessenger extends AbstractHelper
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

    public function __invoke()
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
    public function render()
    {
        $renderer = $this->getView();

        $flashMessenger = $renderer->flashMessenger();

        $flashMessenger->setMessageCloseString($this->messageCloseString);
        $flashMessenger->setMessageOpenFormat($this->messageOpenFormat);
        $flashMessenger->setMessageSeparatorString($this->messageSeparatorString);

        $markup = $flashMessenger->render('error', array('notice--danger'));
        $markup .= $flashMessenger->render('success', array('notice--success'));
        $markup .= $flashMessenger->render('warning', array('notice--warning'));
        $markup .= $flashMessenger->render('info', array('notice--info'));
        $markup .= $flashMessenger->render();

        if (empty($markup)) {
            return '';
        }

        return sprintf($this->wrapper, $markup);
    }
}
