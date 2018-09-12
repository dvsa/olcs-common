<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


use Common\Service\Helper\TranslationHelperService;

abstract class AbstractSection
{
    private $translator;

    private $translationTemplate = 'lva-tmverify-details-checkanswer-';

    public function __construct(TranslationHelperService $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getTranslationTemplate(): string
    {
        return $this->translationTemplate;
    }

    protected function populateTemplate($template, $data): string
    {
        return $this->translator->translateReplace($template, $data);
    }

    public function createSectionFormat()
    {
        return $this->sectionSerialize();
    }

    protected function getSection()
    {
        return $this->getTranslationTemplate() . get_class($this);
    }
}
