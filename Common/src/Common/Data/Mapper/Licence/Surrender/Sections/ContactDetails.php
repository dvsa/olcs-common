<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;
use Common\Service\Helper\UrlHelperService as UrlHelper;

class ContactDetails extends AbstractSection
{
    protected $heading = 'contact-details';

    protected function makeQuestions() {

        $questions = [];

        $primaryPhone = '';
        $secondaryPhone = '';

        foreach ($this->licence['correspondenceCd']['phoneContacts'] as $phoneContact) {
            if ($phoneContact['phoneContactType']['id'] === 'phone_t_primary') {
                $primaryPhone = $phoneContact['phoneNumber'];
            } elseif ($phoneContact['phoneContactType']['id'] === 'phone_t_secondary') {
                $secondaryPhone = $phoneContact['phoneNumber'];
            }
        }

        $questions[] = [
            'label' => $this->translator->translate('contact-number'),
            'answer' => $primaryPhone,
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        $questions[] = [
            'label' => $this->translator->translate('secondary-contact-number'),
            'answer' => $secondaryPhone,
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        $questions[] = [
            'label' => $this->translator->translate('email'),
            'answer' => $this->licence['correspondenceCd']['emailAddress'],
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
                ) . "#" . 'contactDetails'
        ];
    }

}