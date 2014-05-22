<?php
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\AbstractHelper as ZendFormViewHelperAbstractHelper;
use Zend\Form\FormInterface as ZendFormFormInterface;

class FormErrors extends ZendFormViewHelperAbstractHelper
{
    protected static $defaultErrorText = 'There were errors in the form submission';
    protected $messageOpenFormat = '<h3>%s</h3><p>Please try the following:</p><ol class="validation-summary__list"><li class="validation-summary__item">';
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
        $renderer = $this->getView();

        $errorHtml = sprintf($this->messageOpenFormat, $message);

        error_log(var_export($form->getMessages(), 1));

        $messagesArray = array();

        foreach ($form->getMessages() as $fieldName => $messages) {
            foreach ($messages as $validatorName => $message) {
                if ($form->get($fieldName)->getAttribute('id')) {
                    $label = $form->get($fieldName)->getLabel();

                    if (!empty($label)) {
                        $label = $renderer->translate($label);
                    }

                    $messagesArray[] = sprintf(
                        '<a href="#%s">%s</a>',
                        $form->get($fieldName)->getAttribute('id'),
                        (!empty($label) ? $label . ': ' : '') . $message
                    );
                } else {
                    if (is_array($message)) {
                        foreach($message as $value) {
                            $label = $form->get($fieldName)->getLabel();

                            if (!empty($label)) {
                                $label = $renderer->translate($label);
                            }

                            $messagesArray[] = (!empty($label) ? $label . ': ' : '') . $value;
                        }
                    } else {
                        $label = $form->get($fieldName)->getLabel();

                        if (!empty($label)) {
                            $label = $renderer->translate($label);
                        }

                        $messagesArray[] = (!empty($label) ? $label . ': ' : '') . $message;
                    }
                }
            }
        }

        $messageString = implode($this->messageSeparatorString, $messagesArray);

        $errorHtml = $errorHtml . $messageString . $this->messageCloseString;

        return '<div class="validation-summary">' . $errorHtml . '</div>';
    }
}