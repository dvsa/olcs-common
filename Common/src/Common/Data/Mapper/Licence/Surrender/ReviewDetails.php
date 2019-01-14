<?php

namespace Common\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\Licence\Surrender\Sections\LicenceDetails;
use Common\Service\Helper\TranslationHelperService;
use Zend\Mvc\Controller\Plugin\Url;

class ReviewDetails
{
    public static function makeSections(
        array $licence,
        Url $urlHelper,
        TranslationHelperService $translator
    ): array {
        $licenceDetails = new LicenceDetails($licence, $urlHelper, $translator);

        $sections = [
            $licenceDetails->makeSection(),
        ];

        return $sections;
    }
}