<?php

/**
 * Render form
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormElement as ZendFormElement;
use Zend\Form\ElementInterface as ZendElementInterface;
use Zend\Form\Element;
use Common\Form\View\Helper\Traits as AlphaGovTraits;
use Common\Form\Elements\Types\Html;
use Common\Form\Elements\Types\Table;
use Common\Form\Elements\Types\PlainText;
use Common\Form\Elements\InputFilters\ActionLink;

/**
 * Render form
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElement extends ZendFormElement
{

    use AlphaGovTraits\Logger;

    /**
     * The form row output format.
     *
     * @var string
     */
    private static $format = "%s \r\n <p class=\"hint\">%s</p>";

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ZendElementInterface $element
     * @return string
     */
    public function render(ZendElementInterface $element)
    {
        $this->log('Rendering Element: ' . $element->getName(), LOG_INFO);

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            return '';
        }

        if ($element instanceof PlainText) {
            $helper = $renderer->plugin('form_plain_text');
            return $helper($element);
        }


        if ($element instanceof ActionLink) {

            return '<a href="' . $element->getValue() . '">' . $element->getLabel() . '</a>';
        }

        if ($element instanceof Html) {
            return $element->getValue();
        }

        if ($element instanceof Table) {
            return $element->render();
        }

        $markup = parent::render($element);

        if ($element->getOption('hint')) {

            $view = $this->getView();
            $hint = $view->translate($element->getOption('hint'));

            $this->log('Rendering Element Hint: ' . $hint, LOG_INFO);

            $markup = sprintf(self::$format, $markup, $hint);
        }

        return $markup;
    }
}
