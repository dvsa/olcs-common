<?php

/**
 * Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Data\Mapper\Lva;

/**
 * Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerApplication
{
    public static function mapFromErrors($form, array $errors)
    {
        $details = [
            'registeredUser'
        ];
        $formMessages = [];
        foreach ($errors as $field => $message) {
            if (in_array($field, $details)) {
                $formMessages['data'][$field][] = $message;
                unset($errors[$field]);
            }
        }
        $form->setMessages($formMessages);
        return $errors;
    }
}
