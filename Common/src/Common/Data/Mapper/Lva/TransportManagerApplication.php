<?php

/**
 * Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\TransportManager\Sections\Details;
use Common\Service\Helper\TranslationHelperService;

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

    public static function mapForSections(array $transportManagerApplication, array $postData): array
    {
        $details = (new Details(new TranslationHelperService()))->populate($transportManagerApplication);
        $blah =  $details->createSectionFormat();
        $count=0;
        foreach ($postData as $key => $value) {
            if (!in_array($key, ['security', 'form-actions'])) {
                $sectionName = $key;
                $sections[$count]['sectionHeading'] = 'lva-checkanswers-section-' . $sectionName;
                $sections[$count]['change'] = ['sectionName' => $sectionName, 'backText' => ''];
                foreach ($value as $label => $answer) {
                    if (is_array($answer)) {
                        switch ($label) {
                            case "hoursOfWeek":
                                $days = json_encode([
                                    ['label' => 'Monday', 'answer' => 'hoursMon'],
                                    ['label' => 'Tuesday', 'answer' => 'hoursTue'],
                                    ['label' => 'Wednesday', 'answer' => 'hoursWed'],
                                    ['label' => 'Thursday', 'answer' => 'hoursThu'],
                                    ['label' => 'Friday', 'answer' => 'hoursFri'],
                                    ['label' => 'Saturday', 'answer' => 'hoursSat'],
                                    ['label' => 'Sunday', 'answer' => 'hoursSun']
                                ]);

                                foreach ($answer as $key => $value) {
                                    foreach ($value as $weekDay => $hrs) {
                                        $days = str_replace($weekDay, $hrs, $days);
                                    }
                                }
                                $sections[$count + 10]['sectionHeading'] = 'Hours of the Week';
                                $sections[$count + 10]['change'] = ['sectionName' => $sectionName, 'backText' => ''];
                                $sections[$count + 10]['questions'] = json_decode($days, JSON_OBJECT_AS_ARRAY);

                                break;

                            case "birthDate":
                                $answer = (!empty($answer['year']) && !empty($answer['month']) && !empty($answer['day']))
                                    ? sprintf('%04d-%02d-%02d', $answer['year'], $answer['month'], $answer['day'])
                                    : null;
                                $sections[0]['questions'][] = ["label" => "birthDate", "answer" => $answer];
                                break;

                            case "otherLicences":
                                if (empty($sections[$count]['convictions']['rows'])) {
                                    $sections[$count]['questions'][] = ["label"=>"tma-checkanswers-label-".$label, "answer"=>self::getDefaultText()];

                                }
                                break;
                            case "file":
                            case "certificate":
                                if (empty($sections[$sectionName]['fileCount'])) {
                                    $sections[$count]['questions'][] = ["label"=>"tma-checkanswers-label-".$label, "answer"=>self::getDefaultText()];
                                }
                                break;
                            case "convictions":
                                if (empty($sections[$count]['convictions']['rows'])) {
                                    $sections[$count]['questions'][] = ["label"=>"tma-checkanswers-label-".$label, "answer"=>self::getDefaultText()];

                                }

                                break;
                            case "previousLicences":
                                if (empty($sections[$count]['previousLicences']['rows'])) {
                                    $sections[$count]['questions'][] = ["label"=>"tma-checkanswers-label-".$label, "answer"=>self::getDefaultText()];

                                }
                                break;
                            default:
                                $sections[$count]['questions'] = [];
                                if ($sectionName === 'homeAddress' || $sectionName === 'workAddress') {
                                    $label = 'tm-checkanswers-label-' . $sectionName;
                                    $answers = implode('<br />', $answer);
                                    $sections[0]['questions'][] = ["label" => $label, "answer" => $answers];
                                }
                        }
                    } else {
                        $sections[$count]['questions'][] = ['label' => $label, 'answer' => $answer];
                    }
                }
            }

            $count++;
            if ($key === 'details') {
                $label= "name";
                $nameAnswer =['label' => $label, 'answer' => $transportManagerApplication['transportManager']['homeCd']['person']['forename']. " ".$transportManagerApplication['transportManager']['person']['familyName'] ];
                array_unshift($sections[$count]['questions'], $nameAnswer);
            }
        }
        unset($sections[1]);
        unset($sections[2]);
        return $sections;
        /*
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
                'change' => ['sectionName' => 'details', 'backText' => '']
            ],
            [
                'sectionHeading' => "Responsibilities",
                'questions' => [
                    ['label' => 'Which type of transport manager will you be for this licence ?', 'answer' => 'test'],
                    ['label' => 'test', 'answer' => 'Are you the person is our who will be the licenced Operator']
                ],
                'change' => ['sectionName' => 'responsibilities', 'backText' => '']
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
                'change' => ['sectionName' => 'hoursOfWeek', 'backText' => '']
            ],
            [
                'sectionHeading' => "Other Licences",
                'questions' => [
                    ['label' => '', 'answer' => 'non added'],
                ],
                'change' => ['sectionName' => 'otherLicences', 'backText' => '']
            ],
            [
                'sectionHeading' => "Additional Information",
                'questions' => [
                    ['label' => 'Information', 'answer' => 'non added'],
                    ['label' => 'Files', 'answer' => 'non attached']
                ],
                'change' => ['sectionName' => 'additionalInformation', 'backText' => '']
            ],
            [
                'sectionHeading' => "Other Employment",
                'questions' => [
                    ['label' => '', 'answer' => 'non added'],

                ],
                'change' => ['sectionName' => 'otherEmployment', 'backText' => '']
            ],
            [
                'sectionHeading' => "Convictions & Penalities",
                'questions' => [
                    ['label' => '', 'answer' => 'information added'],

                ],
                'change' => ['sectionName' => 'previousConvictions', 'backText' => '']
            ],
            [
                'sectionHeading' => "Revoked Curtailed or Suspended Licences",
                'questions' => [
                    ['label' => '', 'answer' => 'information added'],

                ],
                'change' => ['sectionName' => 'previousLicences', 'backText' => '']
            ],

        ];

        //return $transportManagerApplication;
        */
    }

    public static function getDefaultText()
    {
        return 'None added';
    }
}
