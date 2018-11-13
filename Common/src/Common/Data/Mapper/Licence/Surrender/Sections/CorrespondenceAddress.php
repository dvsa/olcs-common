<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;
use Common\Service\Helper\UrlHelperService as UrlHelper;

class CorrespondenceAddress extends AbstractSection
{
    protected $heading = 'correspondence-address';

    protected function makeQuestions() {

        $questions = [];

        $address = '';

        for ($n = 1; $n <= 4; $n++) {
            $addressLine = trim($this->licence['correspondenceCd']['address']['addressLine' . $n]);
            if (strlen($addressLine) > 0) {
                $address .= strlen($address) > 0 ? "<br>" . $addressLine : $addressLine;
            }
        }

        $questions[] = [
            'label' => $this->translator->translate('address'),
            'answer' => $address,
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        $questions[] = [
            'label' => $this->translator->translate('address_townCity'),
            'answer' => $this->licence['correspondenceCd']['address']['town'],
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        $questions[] = [
            'label' => $this->translator->translate('address_country'),
            'answer' => $this->licence['correspondenceCd']['address']['countryCode']['countryDesc'],
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        return $questions;
    }

    protected function makeChangeLink()
    {
        return [
            'sectionLink' => $this->urlHelper->fromRoute(
                    'licence/surrender/start',
                    [
                        'licence' => $this->licence['id']
                    ]
                ) . "#" . 'correspondenceAddress'
        ];
    }

}