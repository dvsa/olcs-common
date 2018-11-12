<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;


abstract class AbstractSection
{
    protected $displayChangeLinkInHeading = true;

    protected $heading;

    public function makeSection(array $data)
    {
        $questions = $this->makeQuestions($data);

        return [
            'sectionHeading' => $this->heading,
            'changeLinkInHeading' => $this->displayChangeLinkInHeading,
            'change' => $this->makeChangeLink(),
            'questions' => $questions
        ];
    }

    abstract protected function makeQuestions(array $data);

    abstract protected function makeChangeLink();
}