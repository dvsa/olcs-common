<?php

namespace Common\Data\Mapper\Licence\Surrender\Sections;

use Common\Service\Helper\TranslationHelperService;

abstract class AbstractSection
{
    /**
     * @var mixed[]
     */
    public $licence;
    use MakeSectionTrait;

    protected $heading;

    protected $urlHelper;

    protected $translator;

    public function __construct(
        array $licence,
        \Laminas\Mvc\Controller\Plugin\Url $urlHelper,
        TranslationHelperService $translator
    ) {
        $this->licence = $licence;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    abstract protected function makeQuestions();

    abstract protected function makeChangeLink();
}
