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
            [
                'sectionHeading' => "Your Details",
                'questions' => [
                    ['label' => 'name', 'answer' => 'Phil Jowett'],
                    ['label' => 'Date of Birth', 'answer' => '11/11/2000'],
                    ['label' => 'Place of Birth', 'answer' => 'Nottingham'],
                    ['label' => 'Email Address', 'answer' => 'Nottingham'],
                    ['label' => 'Certificate of professional competence', 'answer' => 'certificate attached'],
                    ['label' => 'Home Address', "answer" => ""],
                    ['label' => 'Work Address', "answer" => ""],

                ],
                'change' => ['sectionLink' => 'test', 'backText' => '']
            ],
            [
                'sectionHeading' => "Responsibilities",
                'questions' => [
                    ['label' => 'Which type of transport manager will you be for this licence ?', 'answer' => 'test'],
                    ['label' => 'test', 'answer' => 'Are you the person is our who will be the licenced Operator']
                ],
                'change' => ['sectionLink' => 'test2', 'backText' => '']
            ],
            [
                'sectionHeading' => "Hours per week",
                'questions' => [
                    ['label' => 'Monday', 'answer' => '8'],
                    ['label' => 'Tuesday', 'answer' => '8'],
                    ['label' => 'Wednesday', 'answer' => '8'],
                    ['label' => 'Thursday', 'answer' => '8'],
                    ['label' => 'Friday', 'answer' => '8'],
                    ['label' => 'Saturday', 'answer' => '8'],
                    ['label' => 'Sunday', 'answer' => '8'],

                ],
                'change' => ['sectionLink' => 'test3', 'backText' => '']
            ],
            [
                'sectionHeading' => "Other Licences",
                'questions' => [
                    ['label' => '', 'answer' => 'non added'],
                ],
                'change' => ['sectionLink' => 'test4', 'backText' => '']
            ],
            [
                'sectionHeading' => "Additional Information",
                'questions' => [
                    ['label' => 'Information', 'answer' => 'non added'],
                    ['label' => 'Files', 'answer' => 'non attached']
                ],
                'change' => ['sectionLink' => 'test4', 'backText' => '']
            ],
            [
                'sectionHeading' => "Other Employment",
                'questions' => [
                    ['label' => '', 'answer' => 'non added'],

                ],
                'change' => ['sectionLink' => 'test4', 'backText' => '']
            ],
            [
                'sectionHeading' => "Convictions & Penalities",
                'questions' => [
                    ['label' => '', 'answer' => 'information added'],

                ],
                'change' => ['sectionLink' => 'test5', 'backText' => '']
            ],
            [
                'sectionHeading' => "Revoked Curtailed or Suspended Licences",
                'questions' => [
                    ['label' => '', 'answer' => 'information added'],

                ],
                'change' => ['sectionLink' => 'test6', 'backText' => '']
            ],

        ];

        //return $transportManagerApplication;
    }
}
