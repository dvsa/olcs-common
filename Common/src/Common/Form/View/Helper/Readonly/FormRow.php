<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\Element\Button;
use Zend\Form\ElementInterface;
use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Common\Form\Elements\Types\Table;

/**
 * Class FormRow
 * @package Common\Form\View\Helper\Readonly
 */
class FormRow extends AbstractHelper
{
    /**
     * @var string
     */
    protected $defaultHelper = 'readonlyformitem';

    /**
     * @var array
     */
    protected $classMap = [
        'Zend\Form\Element\Select' => 'readonlyformselect',
        'Zend\Form\Element\DateSelect' => 'readonlyformdateselect',
        'Common\Form\Elements\Types\Table' => 'readonlyformtable'
    ];

    /**
     * @var string
     */
    protected $format = '<li class="%s"><dt>%s</dt><dd>%s</dd></li>';

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Retrieve the FormElement helper
     *
     * @param \Zend\Form\ElementInterface $element
     * @return Callable
     */
    protected function getElementHelper(ElementInterface $element)
    {
        foreach ($this->classMap as $class => $plugin) {
            if ($element instanceof $class) {
                return $this->getView()->plugin($plugin);
            }
        }

        return $this->getView()->plugin($this->defaultHelper);
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if (in_array($element->getAttribute('type'), ['hidden', 'submit']) ||
            $element instanceof Button ||
            $element->getOption('remove_if_readonly')
        ) {
            //bail early if we don't want to display this type of element
            return '';
        }

        if ($element instanceof Table) {
            // we dont want Tables to be rendered with a label / value so just return the result of the view helper
            $elementHelper = $this->getElementHelper($element);
            return  $elementHelper($element);
        }

        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper($element);
        $label = $element->getLabel();

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
        }

        if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
            $label = $escapeHtmlHelper($label);
        }

        $value = $elementHelper($element);
        $class = $this->getClass($element);

        return sprintf($this->format, $class, $label, $value);
    }

    /**
     * @param $element
     * @return string
     */
    public function getClass($element)
    {
        $class = 'definition-list__item';

        if (
            $element->getAttribute('type') == 'textarea' ||
            ($element->getAttribute('type') == 'select' && $element->getAttribute('multiple'))
        ) {
            $class .= ' full-width';
        }

        return $class;
    }
}
