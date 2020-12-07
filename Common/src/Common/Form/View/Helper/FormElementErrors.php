<?php

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Laminas\Form\View\Helper\FormElementErrors as LaminasFormElementErrors;
use Traversable;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Common\Form\Elements\Validators\Messages\ValidationMessageInterface;

/**
 * Render form element errors
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormElementErrors extends LaminasFormElementErrors
{
    protected $attributes = [
        'class' => 'error__text',
    ];

    /**
     * Render validation errors for the provided $element
     *
     * @param ElementInterface $element    Element to render errors for
     * @param array            $attributes HTML attributes to add to the render markup
     *
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

        $elementShouldEscape = $element->getOption('shouldEscapeMessages');

        // Flatten message array
        $escapeHtml      = $this->getEscapeHtmlHelper();
        $messagesToPrint = array();
        array_walk_recursive(
            $messages,
            function ($item) use (&$messagesToPrint, $escapeHtml, $renderer, $elementShouldEscape) {

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

                if ($shouldEscape && $elementShouldEscape !== false) {
                    $item = $escapeHtml($item);
                }

                $messagesToPrint[] = ucfirst($item);
            }
        );

        if (empty($messagesToPrint)) {
            return '';
        }

        $markup = '';
        foreach ($messagesToPrint as $message) {
            $markup .= sprintf('<p%s>%s</p>', $attributes, $message);
        }

        return $markup;
    }
}
