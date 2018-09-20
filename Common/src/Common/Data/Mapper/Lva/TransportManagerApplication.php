<?php

/**
 * Transport Manager Application
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\TransportManager\Sections\AdditionalInformation;
use Common\Data\Mapper\Lva\TransportManager\Sections\ConvictionsPenalties;
use Common\Data\Mapper\Lva\TransportManager\Sections\Details;
use Common\Data\Mapper\Lva\TransportManager\Sections\HoursOfWork;
use Common\Data\Mapper\Lva\TransportManager\Sections\OtherLicences;
use Common\Data\Mapper\Lva\TransportManager\Sections\Responsibilities;
use Common\Data\Mapper\Lva\TransportManager\Sections\RevokedLicences;
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

    public static function mapForSections(array $transportManagerApplication, TranslationHelperService $translationHelperService): array
    {

        $data  = [];
        $details = (new Details($translationHelperService))->populate($transportManagerApplication);
        $detailsQuestions = $details->createSectionFormat();
        $data [] = $details->makeSection('Details', $detailsQuestions);
        $hours = (new HoursOfWork($translationHelperService))->populate($transportManagerApplication);
        $hoursQuestions = $hours->createSectionFormat();
        $data [] = $hours->makeSection('HoursOfWork', $hoursQuestions);
        $responsibilities = (new Responsibilities($translationHelperService))->populate($transportManagerApplication);
        $responsibilitiesQuestions = $responsibilities->createSectionFormat();
        $data [] =$responsibilities->makeSection('Responsibilities', $responsibilitiesQuestions);
        $licences =  (new OtherLicences($translationHelperService))->populate($transportManagerApplication);
        $licencesQuestions = $licences->createSectionFormat();
        $data [] = $licences->makeSection('otherLicences', $licencesQuestions);
        $additionalInfo = (new AdditionalInformation($translationHelperService))->populate($transportManagerApplication);
        $additionalInfoQuestions = $additionalInfo->createSectionFormat();
        $data [] = $additionalInfo->makeSection('additionalInfo', $additionalInfoQuestions);
        $convictions= (new ConvictionsPenalties($translationHelperService))->populate($transportManagerApplication);
        $convictionsQuestions = $convictions->createSectionFormat();
        $data [] = $convictions->makeSection('convictions', $convictionsQuestions);
        $revocations = (new RevokedLicences($translationHelperService))->populate($transportManagerApplication);
        $revocationQuestions = $revocations->createSectionFormat();
        $data [] = $revocations->makeSection('revocations', $revocationQuestions);

        return $data;
    }

    public static function getDefaultText()
    {
        return 'None added';
    }
}
