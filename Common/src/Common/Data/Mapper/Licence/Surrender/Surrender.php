<?php

namespace Common\Data\Mapper\Licence\Surrender;

use Common\Service\Helper\TranslationHelperService;

class Surrender
{

    public static function mapForReviewContactDetailsSections(array $licence, TranslationHelperService $translationHelperService): array
    {

        $data  = [];
        $details = (new Details($translationHelperService))->populate($transportManagerApplication);
        $detailsQuestions = $details->createSectionFormat();
        $data [] = $details->makeSection('Details', $detailsQuestions, 'details');

        return $data;
    }

}