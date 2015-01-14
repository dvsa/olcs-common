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
use Common\Form\Elements\Types\Html;
use Common\Form\Elements\Types\HtmlTranslated;
use Common\Form\Elements\Types\Table;
use Common\Form\Elements\Types\PlainText;
use Common\Form\Elements\InputFilters\ActionLink;
use Common\Form\Elements\Types\TrafficAreaSet;

/**
 * Render form
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElement extends ZendFormElement
{

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
        if (!$element->getAttribute('id')) {
            $element->setAttribute('id', $element->getName());
        }

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            return '';
        }

        if ($element instanceof TrafficAreaSet) {
            $value = $element->getValue();

            $translationPrefix = (string)$element->getOption('hint-prefix');

            $view = $this->getView();

            return sprintf(
                '<p>%s</p><h3>%s</h3><p class="hint">%s</p>',
                $view->translate('trafficAreaSet.trafficArea'),
                $view->translate($value),
                sprintf(
                    $view->translate('trafficAreaSet.hint' . $translationPrefix),
                    // @todo replace with real link
                    '#'
                )
            );
        }

        if ($element instanceof PlainText) {
            $helper = $renderer->plugin('form_plain_text');
            return $helper($element);
        }

        if ($element instanceof ActionLink) {

            $route = $element->getOption('route');
            if (!empty($route)) {
                $url = $this->getView()->url($route, array(), array(), true);
            } else {
                $url = $element->getValue();
            }

            $class = '';

            if ($element->getAttribute('class')) {
                $class = $element->getAttribute('class');
            }

            return '<a href="' . $url . '" class="' . $class . '">' . $element->getLabel() . '</a>';
        }

        if ($element instanceof HtmlTranslated) {
            $tokens = $element->getTokens();
            $translated = [];
            if (is_array($tokens) && count($tokens)) {
                foreach ($tokens as $token) {
                    $translated[] = $this->getView()->translate($token);
                }
                return vsprintf($element->getValue(), $translated);
            }

            $value = $element->getValue();

            if (empty($value)) {
                return '';
            }

            return $this->getView()->translate($element->getValue());
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

            $markup = sprintf(self::$format, $markup, $hint);
        }

        return $markup;
    }
}
