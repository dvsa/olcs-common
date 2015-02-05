<?php

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;
use Traversable;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Common\Form\Elements\Validators\Messages\ValidationMessageInterface;

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElementErrors extends ZendFormElementErrors
{
    /**
     * Render validation errors for the provided $element
     * @NOTE: Pretty much identical to Zends version except we translate the messages here
     *
     * @param  ElementInterface $element
     * @param  array $attributes
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element, array $attributes = array())
    {
        $messages = $element->getMessages();

        if (empty($messages)) {
            return '';
        }

        if (!is_array($messages) && !$messages instanceof Traversable) {
            throw new Exception\DomainException(
                sprintf(
                    '%s expects that $element->getMessages() will return an array or Traversable; received "%s"',
                    __METHOD__,
                    (is_object($messages) ? get_class($messages) : gettype($messages))
                )
            );
        }

        // Prepare attributes for opening tag
        $attributes = array_merge($this->attributes, $attributes);
        $attributes = $this->createAttributesString($attributes);
        if (!empty($attributes)) {
            $attributes = ' ' . $attributes;
        }

        $renderer = $this->getView();

        // Flatten message array
        $escapeHtml      = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();
        array_walk_recursive(
            $messages,
            function ($item) use (&$messagesToPrint, $escapeHtml, $renderer) {

                $shouldTranslate = true;
                $shouldEscape = true;

                if ($item instanceof ValidationMessageInterface) {

                    $shouldTranslate = $item->shouldTranslate();
                    $shouldEscape = $item->shouldEscape();
                    $item = $item->getMessage();
                }

                if ($shouldTranslate) {
                    $item = $renderer->translate($item);
                }

                if ($shouldEscape) {
                    $item = $escapeHtml($item);
                }

                $messagesToPrint[] = ucfirst($item);
            }
        );

        if (empty($messagesToPrint)) {
            return '';
        }

        // Generate markup
        $markup  = sprintf($this->getMessageOpenFormat(), $attributes);
        $markup .= implode($this->getMessageSeparatorString(), $messagesToPrint);
        $markup .= $this->getMessageCloseString();

        return $markup;
    }
}
