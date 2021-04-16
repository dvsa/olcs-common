<?php

declare(strict_types=1);

namespace Common\Form\Elements\Validators\Messages;

use Laminas\Form\ElementInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Common\Helper\Str;

/**
 * @see FormElementMessageFormatterFactory
 * @see \CommonTest\Form\Elements\Validators\Messages\FormElementMessageFormatterTest
 */
class FormElementMessageFormatter
{
    const FIELD_LABEL_PLACEHOLDER = '{{fieldLabel}}';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $messagesReplacementProviders = [];

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $messageKey
     * @param string|callable|Closure $defaultMessageOrProvider
     */
    public function enableReplacementOfMessage(string $messageKey, $defaultMessageOrProvider)
    {
        if (is_string($defaultMessageOrProvider)) {
            $defaultMessageOrProvider = function () use ($defaultMessageOrProvider) {
                return $defaultMessageOrProvider;
            };
        }
        assert(is_callable($defaultMessageOrProvider), 'Expected default message provider to be callable or string');
        $this->messagesReplacementProviders[$messageKey] = $defaultMessageOrProvider;
    }

    /**
     * @param string $messageKey
     * @return callable|Closure|null
     */
    public function getReplacementFor(string $messageKey)
    {
        return $this->messagesReplacementProviders[$messageKey] ?? null;
    }

    /**
     * @param ElementInterface $element
     * @param string|ValidationMessageInterface $message
     * @param mixed $messageKey
     * @return string
     */
    public function formatElementMessage(ElementInterface $element, $message, $messageKey = null): string
    {
        $shouldTranslate = true;
        if ($message instanceof ValidationMessageInterface) {
            $shouldTranslate = $message->shouldTranslate();
            $message = $message->getMessage();
        }

        $label = $this->getElementShortLabel($element);
        if (empty($label)) {
            $message = $this->replaceDefaultValidationMessages($element, $messageKey, $message);
            if ($shouldTranslate) {
                $message = $this->translator->translate($message);
            }
            $message = $this->replaceMessageVariables($element, $message);
        } else {
            $label = $this->translator->translate($label) . ': ';
            $message = $this->translator->translate($label . $message);
        }

        // If there is a specified custom error message, use that
        if ($this->getElementCustomErrorMessage($element)) {
            $message = $this->getElementCustomErrorMessage($element);

            // Translate the message since we have now got new untranslated content
            $message = $this->translator->translate($message);
        }

        return ucfirst($message);
    }

    /**
     * Get the custom error message if it exists
     *
     * @param ElementInterface $element Element to get cusomt error message from
     *
     * @return string
     */
    protected function getElementCustomErrorMessage(ElementInterface $element)
    {
        $errorMessage = $element->getOption('error-message');

        if ($errorMessage) {
            return $errorMessage;
        }

        return '';
    }

    /**
     * Get the short label for an element if it exists
     *
     * @param ElementInterface $element
     * @return string
     */
    protected function getElementShortLabel(ElementInterface $element): string
    {
        $label = $element->getOption('short-label');
        return $label ? $label : '';
    }

    /**
     * @param ElementInterface $element
     * @param string $message
     * @return string
     */
    protected function replaceMessageVariables(ElementInterface $element, string $message): string
    {
        $labelText = $this->getFieldLabelForElement($element);

        // Replace field label message variable
        $message = str_replace(static::FIELD_LABEL_PLACEHOLDER, $labelText, $message);

        return $message;
    }

    /**
     * Determines whether an element has values for each message variable used in a message.
     *
     * @param ElementInterface $element
     * @param string $message
     * @return bool
     */
    protected function elementHasVariablesInMessage(ElementInterface $element, string $message): bool
    {
        if (str_contains($message, static::FIELD_LABEL_PLACEHOLDER)) {
            $elementLabel = $this->getFieldLabelForElement($element);
            if (empty($elementLabel)) {
                return false;
            }
            if (Str::containsHtml($elementLabel)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ElementInterface $element
     * @return string
     */
    protected function getFieldLabelForElement(ElementInterface $element): string
    {
        $label = $element->getLabel();
        if (! is_string($label)) {
            return '';
        }

        $label = trim($label);
        if (empty($label)) {
            return '';
        }

        return $this->translator->translate($label);
    }

    /**
     * @param ElementInterface $element
     * @param mixed $messageKey
     * @param string $message
     * @return string
     */
    protected function replaceDefaultValidationMessages(ElementInterface $element, $messageKey, string $message): string
    {
        if ($this->isDefaultMessageForKey($messageKey, $message)) {
            $elementType = $element->getAttribute('type');
            $replacementMessage = $this->getReplacementForDefaultElementMessage($element, $messageKey, $elementType);
            if ($replacementMessage !== false) {
                return $replacementMessage;
            }

            $replacementMessage = $this->getReplacementForDefaultElementMessage($element, $messageKey, 'default');
            if ($replacementMessage !== false) {
                return $replacementMessage;
            }
        }
        return $message;
    }

    /**
     * @param ElementInterface $element
     * @param $messageKey
     * @param string|null $elementType
     * @return false|string
     */
    protected function getReplacementForDefaultElementMessage(ElementInterface $element, $messageKey, string $elementType = null)
    {
        if (null === $elementType) {
            return false;
        }

        $translationKey = sprintf('validation.element.%s.%s', $elementType, $messageKey);
        $replacementMessage = $this->translator->translate($translationKey);
        if ($replacementMessage !== $translationKey && $this->elementHasVariablesInMessage($element, $replacementMessage)) {
            return $translationKey;
        }
        return false;
    }

    /**
     * @param $messageKey
     * @param string $message
     * @return bool
     */
    protected function isDefaultMessageForKey($messageKey, string $message): bool
    {
        $defaultMessageProvider = $this->messagesReplacementProviders[$messageKey] ?? null;
        if (null === $defaultMessageProvider) {
            return false;
        }

        $defaultMessage = $defaultMessageProvider($messageKey);
        if ($defaultMessage === $message) {
            return true;
        }

        $translatedDefaultMessage = $this->translator->translate($defaultMessage);
        if ($translatedDefaultMessage === $message) {
            return true;
        }

        return false;
    }
}
