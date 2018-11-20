<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;

use Common\Service\Helper\TranslationHelperService;

abstract class AbstractSection
{
    protected $displayChangeLinkInHeading = true;

    protected $heading;

    protected $urlHelper;

    protected $translator;

    public function __construct(
        array $licence,
        \Zend\Mvc\Controller\Plugin\Url $urlHelper,
        TranslationHelperService $translator
    ) {
        $this->licence = $licence;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    public function makeSection()
    {
        $questions = $this->makeQuestions();

        return [
            'sectionHeading' => $this->translator->translate($this->heading),
            'changeLinkInHeading' => $this->displayChangeLinkInHeading,
            'change' => $this->makeChangeLink(),
            'questions' => $questions
        ];
    }

    abstract protected function makeQuestions();

    abstract protected function makeChangeLink();
}
