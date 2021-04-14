<?php

namespace Common\Form\View\Helper;

use Common\Form\Elements\Validators\Messages\FormElementMessageFormatter;
use Laminas\Form\View\Helper\FormElementErrors as LaminasFormElementErrors;
use Laminas\I18n\Translator\TranslatorInterface;
use Traversable;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Common\Form\Elements\Validators\Messages\ValidationMessageInterface;

/**
 * @see FormElementErrorsFactory
 * @see \CommonTest\Form\View\Helper\FormElementErrorsTest
 */
class FormElementErrors extends LaminasFormElementErrors
{
    protected $attributes = [
        'class' => 'error__text',
    ];

    /**
     * @var FormElementMessageFormatter
     */
    protected $messageFormatter;

    /**
     * @param FormElementMessageFormatter $messageFormatter
     * @param TranslatorInterface $translator
     */
    public function __construct(FormElementMessageFormatter $messageFormatter, TranslatorInterface $translator)
    {
        $this->messageFormatter = $messageFormatter;
        $this->setTranslator($translator);
    }

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

        $elementShouldEscape = $element->getOption('shouldEscapeMessages');
        $escaper = $this->getEscapeHtmlHelper();

        // Flatten message array
        $messagesToPrint = array();
        array_walk_recursive($messages, function ($item, $itemKey) use (&$messagesToPrint, $elementShouldEscape, $element, $escaper) {
            $shouldEscape = true;
            if ($item instanceof ValidationMessageInterface) {
                $shouldEscape = $item->shouldEscape();
            }
            $message = $this->messageFormatter->formatElementMessage($element, $item, $itemKey);
            if ($shouldEscape && $elementShouldEscape !== false) {
                $message = call_user_func($escaper, $message);
            }
            $messagesToPrint[] = $message;
        });

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
