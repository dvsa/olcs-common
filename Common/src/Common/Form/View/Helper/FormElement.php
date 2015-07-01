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
use Common\Form\Elements\Types\GuidanceTranslated;
use Common\Form\Elements\Types\TermsBox;
use Common\Form\Elements\Types\Table;
use Common\Form\Elements\Types\PlainText;
use Common\Form\Elements\InputFilters\ActionLink;
use Common\Form\Elements\Types\TrafficAreaSet;
use Common\Form\Elements\Types\AttachFilesButton;

/**
 * Render form
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElement extends ZendFormElement
{
    const GUIDANCE_WRAPPER = '<div class="guidance">%s</div>';
    const TERMS_BOX_WRAPPER = '<div %s>%s</div>';
    const FILE_CHOOSE_WRAPPER
        = '<ul class="%s"><li class="%s"><label class="%s">%s %s</label><p class="%s">%s</p></li></ul>';

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

            $translationSuffix = (string)$element->getOption('hint-suffix');

            $view = $this->getView();

            return sprintf(
                '<p>%s</p><h3>%s</h3><p class="hint">%s</p>',
                $view->translate('trafficAreaSet.trafficArea'),
                $view->translate($value),
                sprintf(
                    $view->translate('trafficAreaSet.hint' . $translationSuffix),
                    // @todo replace with real link
                    'http://www.google.co.uk'
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

            if ($element instanceof GuidanceTranslated) {
                $wrapper = self::GUIDANCE_WRAPPER;
            } else {
                $wrapper = '%s';
            }

            $tokens = $element->getTokens();

            if (is_array($tokens) && count($tokens)) {

                $translated = [];

                foreach ($tokens as $token) {
                    $translated[] = $this->getView()->translate($token);
                }
                return sprintf($wrapper, vsprintf($this->getView()->translate($element->getValue()), $translated));
            }

            $value = $element->getValue();

            if (empty($value)) {
                return '';
            }

            return sprintf($wrapper, $this->getView()->translate($element->getValue()));
        }

        if ($element instanceof TermsBox) {

            $attributes = $element->getAttributes();

            if (!isset($attributes['class'])) {
                $attributes['class'] = '';
            }

            $attributes['class'] .= ' terms--box';

            $attr = $renderer->form()->createAttributesString($attributes);

            return sprintf(self::TERMS_BOX_WRAPPER, $attr, $this->getView()->translate($element->getValue()));
        }

        if ($element instanceof Html) {
            return $element->getValue();
        }

        if ($element instanceof Table) {
            return $element->render();
        }

        if ($element instanceof AttachFilesButton) {

            $attributes = $element->getAttributes();
            if (!isset($attributes['class'])) {
                $attributes['class'] = '';
            }

            $attributes['class'] .= ' attach-action__input';

            $element->setAttributes($attributes);

            $label = $element->getOption('value');
            $hint = $element->getOption('hint');

            return sprintf(
                self::FILE_CHOOSE_WRAPPER,
                'attach-action__list',
                'attach-action',
                'attach-action__label',
                $label,
                parent::render($element),
                'attach-action__hint',
                $hint
            );
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
