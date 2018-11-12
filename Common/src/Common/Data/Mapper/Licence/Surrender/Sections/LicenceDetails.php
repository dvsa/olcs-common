<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;

class LicenceDetails extends AbstractSection
{

    protected $heading = 'Licence details';

    protected function makeQuestions(array $licence) {

        $questions = [];

        $questions[] = [
            'label' => 'Licence number',
            'answer' => $licence['licNo'],
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        $questions[] = [
            'label' => 'Name of licence holder',
            'answer' => $licence['organisation']['name'],
            'changeLinkInHeading' => $this->displayChangeLinkInHeading
        ];

        if (count($licence['organisation']['tradingNames'])) {

            foreach ($licence['organisation']['tradingNames'] as $tradingData) {
                $tradingNames = $tradingData['name'];
            }

            $tradingName = implode(', ', $tradingNames);

            $questions[] = [
                'label' => 'Trading name',
                'answer' => $tradingName,
                'changeLinkInHeading' => $this->displayChangeLinkInHeading
            ];
        }

        return $questions;
    }

    protected function makeChangeLink()
    {
        return false;
    }
}
