<?php

declare(strict_types=1);

namespace Common\Form\Elements\Validators\Messages;

use Laminas\Form\ElementInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\NotEmpty;

/**
 * @see FormElementMessageFormatterFactory
 * @see \CommonTest\Form\Elements\Validators\Messages\FormElementMessageFormatterTest
 */
class FormElementMessageFormatter
{
    const FIELD_LABEL_PLACEHOLDER = '{{fieldLabel}}';

    protected const VALIDATOR_MESSAGE_TEMPLATE_MAP = [
        NotEmpty::IS_EMPTY => NotEmpty::class,
    ];

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * FormElementMessageFormatter constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
        $rawLabelText = $this->getFieldLabelForElement($element);
        $labelText = empty($rawLabelText) ? '' : $this->translator->translate($rawLabelText);

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
        if (str_contains($message, static::FIELD_LABEL_PLACEHOLDER) && empty($this->getFieldLabelForElement($element))) {
            return false;
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
            $label = '';
        }
        return trim($label);
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
        $validatorClass = static::VALIDATOR_MESSAGE_TEMPLATE_MAP[$messageKey] ?? null;
        if (null === $validatorClass) {
            return false;
        }

        $validator = new $validatorClass();
        assert($validator instanceof AbstractValidator, 'Expected instance of AbstractValidator');
        $defaultMessageTemplate = $validator->getMessageTemplates()[$messageKey] ?? null;
        if ($defaultMessageTemplate === $message) {
            return true;
        }

        $translatedDefaultMessageTemplate = $this->translator->translate($defaultMessageTemplate);
        if ($translatedDefaultMessageTemplate === $message) {
            return true;
        }

        return false;
    }
}
