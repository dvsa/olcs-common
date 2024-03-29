<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;

use Common\Service\Helper\TranslationHelperService;

abstract class AbstractSection
{
    private $translator;

    private $translationTemplate = 'lva-tmverify-details-checkanswer-';

    private $displayChangeLinkInHeading = true;

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

    /**
     * sortByCreated
     *
     * @param $items
     *
     * @return array
     */
    protected function sortByCreated(array $items): array
    {
        if (count($items) > 1) {
            usort($items, fn($a, $b) => strtotime($b['createdOn']) - strtotime($a['createdOn']));
        }
        return $items;
    }

    public function makeSection(string $section, array $items, $changeName): array
    {
        return [
            'sectionHeading' => $this->getTranslationTemplate() . $section,
            'questions' => $this->makeChangeAnswerSections($items),
            'changeLinkInHeading' => $this->displayChangeLinkInHeading,
            'change' => ['sectionName' => $changeName, 'backText' => '']
        ];
    }

    private function makeChangeAnswerSections(array $items): array
    {
        $questionSections = [];
        foreach ($items as $question => $answer) {
            $questionSections[] = [
                'label' => $question,
                'answer' => $this->translator->translate($answer),
                'changeLinkInHeading' => $this->displayChangeLinkInHeading
            ];
        }
        return $questionSections;
    }
}
