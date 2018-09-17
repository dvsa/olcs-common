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
        $details = (new Details($translationHelperService))->populate($transportManagerApplication);
        $blah =  $details->createSectionFormat();
        $count=0;
        $hoursOfWork = (new HoursOfWork($translationHelperService))->populate($transportManagerApplication);
        $hours = $hoursOfWork->createSectionFormat();
        $resp = (new Responsibilities($translationHelperService))->populate($transportManagerApplication);
        $res = $resp->createSectionFormat();

        $lic =  (new OtherLicences($translationHelperService))->populate($transportManagerApplication);
        $licn = $lic->createSectionFormat();

        $add =  (new AdditionalInformation($translationHelperService))->populate($transportManagerApplication);
        $add2 = $add->createSectionFormat();

        $con = (new ConvictionsPenalties($translationHelperService))->populate($transportManagerApplication);
        $con2 = $con->createSectionFormat();

        return [];
    }

    public static function getDefaultText()
    {
        return 'None added';
    }
}
