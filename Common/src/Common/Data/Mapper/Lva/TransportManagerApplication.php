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

    public static function mapForSections(array $transportManagerApplication): array
    {
        return [
            ['sectionHeading' => "Your Details", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],
            ['sectionHeading' => "Responsibilities", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],
            ['sectionHeading' => "Hours per week", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],
            ['sectionHeading' => "Additional Information", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],
            ['sectionHeading' => "Convictions & Penalities", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],
            ['sectionHeading' => "Revoked Curtailed or Suspended Licences", 'questions'=>[['label'=>'test','answer'=>'test'],['label'=>'test','answer'=>'test']],'change'=>['sectionLink'=>'test','backText'=>'']],

        ];

        //return $transportManagerApplication;
    }

}
