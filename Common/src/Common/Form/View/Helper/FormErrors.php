<?php

/**
 * Form errors view helper
 *
 * @author Someone <someone@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper as ZendFormViewHelperAbstractHelper;
use Zend\Form\FormInterface as ZendFormFormInterface;
use Zend\Form\Fieldset;

/**
 * Form errors view helper
 *
 * @author Someone <someone@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormErrors extends ZendFormViewHelperAbstractHelper
{
    protected $defaultErrorText = 'form-errors';

    protected $messageOpenFormat = '<h3>%s</h3>
        <ol class="validation-summary__list"><li class="validation-summary__item">';

    protected $messageCloseString = '</li></ol>';

    protected $messageSeparatorString = '</li><li class="validation-summary__item">';

    /**
     * Invoke as function
     *
     * @param  ZendFormFormInterface $form The form object
     * @return Form
     */
    public function __invoke(ZendFormFormInterface $form = null, $message = null)
    {
        if (!$form) {
            return $this;
        }

        if (!$message) {
            $message = $this->translate($this->defaultErrorText);
        }

        if ($form->hasValidated() && !$form->isValid()) {
            return $this->render($form, $message);
        }

        return null;
    }

    /**
     * Helper method to translate strings
     *
     * @param string $text
     * @return string
     */
    protected function translate($text)
    {
        $renderer = $this->getView();

        return $renderer->translate($text);
    }

    /**
     * Renders the error messages.
     *
     * @param ZendFormFormInterface $form
     *
     * return string
     */
    public function render(ZendFormFormInterface $form, $message)
    {
        $errorHtml = sprintf($this->messageOpenFormat, $message);

        $messagesArray = $this->getFlatMessages($form->getMessages(), $form);

        if (empty($messagesArray)) {
            return '';
        }

        $messageString = implode($this->messageSeparatorString, $messagesArray);

        $errorHtml = $errorHtml . $messageString . $this->messageCloseString;

        return '<div class="validation-summary">' . $errorHtml . '</div>';
    }

    /**
     * Recurse the messages array and flatten them out
     *
     * @param array $messages
     * @param Fieldset $fieldset
     * @return array
     */
    private function getFlatMessages($messages, $fieldset)
    {
        $flatMessages = [];

        foreach ($messages as $field => $message) {
            if (is_array($message)) {
                $flatMessages = array_merge(
                    $flatMessages,
                    $this->getFlatMessages($message, $fieldset->get($field))
                );
            } else {
                $flatMessages[] = $this->formatMessage($message, $fieldset);
            }
        }

        return $flatMessages;
    }

    /**
     * Format the message
     *
     * @param string $message
     * @param Element $element
     * @return string|array
     */
    private function formatMessage($message, $element)
    {
        // We translate the initial message, as they are not always translated before they get here
        $message = $this->translate($message);

        // Grab the short-label if it's set
        $label = $this->getShortLabel($element);

        if ($label == '') {
            $message = ucfirst($message);
        } else {
            $label = '\'' . $this->translate($label) . '\' ';

            // @NOTE We pass this back through the translator, so individual messages can be tweaked for a better UX
            $message = $this->translate($label . $message);
        }

        // Try and find an element to link to
        $anchor = $this->getNamedAnchor($element);

        // If we have an ID
        if (!empty($anchor)) {
            return sprintf('<a href="#%s">%s</a>', $anchor, $message);
        }

        return $message;
    }

    /**
     * Try and find an anchor to link to
     *
     * @param Element $element
     * @return string
     */
    private function getNamedAnchor($element)
    {
        $fieldsetAttributes = $element->getOption('fieldset-attributes');

        if (isset($fieldsetAttributes['id'])) {
            return $fieldsetAttributes['id'];
        }

        $labelAttributes = $element->getOption('label_attributes');

        if (isset($labelAttributes['id'])) {
            return $labelAttributes['id'];
        }

        $id = $element->getAttribute('id');

        if ($id) {
            return $id;
        }

        return null;
    }

    /**
     * Grab the label if it exists
     *
     * @param string $element
     * @return string
     */
    private function getShortLabel($element)
    {
        $label = $element->getOption('short-label');

        if ($label) {
            return $label;
        }

        return '';
    }
}
