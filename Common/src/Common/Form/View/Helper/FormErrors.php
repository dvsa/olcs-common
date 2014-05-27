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

/**
 * Form errors view helper
 *
 * @author Someone <someone@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormErrors extends ZendFormViewHelperAbstractHelper
{

    protected static $defaultErrorText = 'There were errors in the form submission';
    protected $messageOpenFormat = '<h3>%s</h3>
        <p>Please try the following:</p>
        <ol class="validation-summary__list"><li class="validation-summary__item">';
    protected $messageCloseString = '</li"></ol>';
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
            $message = self::$defaultErrorText;
        }

        if ($form->hasValidated() && !$form->isValid()) {

            return $this->render($form, $message);
        }

        return null;
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

        $messagesArray = array();

        foreach ($form->getMessages() as $fieldName => $fieldMessages) {

            foreach ($fieldMessages as $message) {

                $formattedMessage = $this->formatMessage($message, $form, $fieldName);

                if (is_array($formattedMessage)) {
                    $messagesArray = array_merge($messagesArray, $formattedMessage);
                } else {
                    $messagesArray[] = $formattedMessage;
                }
            }
        }

        $messageString = implode($this->messageSeparatorString, $messagesArray);

        $errorHtml = $errorHtml . $messageString . $this->messageCloseString;

        return '<div class="validation-summary">' . $errorHtml . '</div>';
    }

    /**
     * Format the message
     *
     * @param string $message
     * @param Form $form
     * @param string $fieldName
     * @return string|array
     */
    private function formatMessage($message, $form, $fieldName)
    {
        $renderer = $this->getView();

        $label = $form->get($fieldName)->getLabel();

        if (!empty($label)) {
            $label = $renderer->translate($label) . ': ';
        }

        // If we have an ID
        if ($form->get($fieldName)->getAttribute('id')) {
            $id = $form->get($fieldName)->getAttribute('id');
            $message = $renderer->translate($message);

            return sprintf('<a href="#%s">%s</a>', $id, $label . $message);
        }

        if (!is_array($message)) {
            $message = $renderer->translate($message);
            return $label . $message;
        }

        $messages = array();

        foreach ($message as $value) {
            $value = $renderer->translate($value);
            $messages[] = $label . $value;
        }

        return $messages;
    }
}
