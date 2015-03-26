<?php

/**
 * Form errors view helper
 *
 * @author Someone <someone@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\FormInterface;
use Zend\Form\Fieldset;
use Common\Form\Elements\Validators\Messages\ValidationMessageInterface;

/**
 * Form errors view helper
 *
 * @author Someone <someone@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormErrors extends AbstractHelper
{
    /**
     * Invoke as function
     *
     * @param  ZendFormFormInterface $form The form object
     * @return Form
     */
    public function __invoke(FormInterface $form = null)
    {
        if (!$form) {
            return $this;
        }

        return $this->render($form);
    }

    /**
     * Renders the error messages.
     *
     * @param FormInterface $form
     *
     * return string
     */
    public function render(FormInterface $form)
    {
        if (!$form->hasValidated() || $form->isValid()) {
            return '';
        }

        $messages = $form->getMessages();

        if (empty($messages)) {
            return '';
        }

        $messagesOpenFormat = '
<div class="validation-summary">
    <h3>%s</h3>
    <ol class="validation-summary__list">
        <li class="validation-summary__item">
            ';

        $messageSeparatorString = '
        </li>
        <li class="validation-summary__item">
            ';

        $messageCloseString = '
        </li>
    </ol>
</div>';

        return sprintf($messagesOpenFormat, $this->translate('form-errors'))
            . implode($messageSeparatorString, $this->getFlatMessages($messages, $form))
            . $messageCloseString;
    }

    /**
     * Recurse the messages array and flatten them out
     *
     * @param array $messages
     * @param Fieldset $fieldset
     * @return array
     */
    protected function getFlatMessages($messages, $fieldset)
    {
        $flatMessages = [];

        foreach ($messages as $field => $message) {

            if ($fieldset instanceof Fieldset) {
                if ($fieldset->has($field)) {
                    $element = $fieldset->get($field);
                } else {
                    $element = $fieldset;
                }
            } else {
                $element = $fieldset;
            }

            if (is_array($message)) {
                $flatMessages = array_merge(
                    $flatMessages,
                    $this->getFlatMessages($message, $element)
                );
            } else {
                $flatMessages[] = $this->formatMessage($message, $element);
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
    protected function formatMessage($message, $element)
    {
        if ($message instanceof ValidationMessageInterface) {

            $msg = $message->getMessage();

            if ($message->shouldTranslate()) {
                $msg = $this->translate($msg);
            }

            return $msg;
        }

        // We translate the initial message, as they are not always translated before they get here
        $message = $this->translate($message);

        // Grab the short-label if it's set
        $label = $this->getShortLabel($element);

        if ($label == '') {
            $message = ucfirst($message);
        } else {
            $label = $this->translate($label) . ': ';

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
    protected function getNamedAnchor($element)
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
    protected function getShortLabel($element)
    {
        $label = $element->getOption('short-label');

        if ($label) {
            return $label;
        }

        return '';
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
}
