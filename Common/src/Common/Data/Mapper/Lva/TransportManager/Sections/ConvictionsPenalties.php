<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;

class ConvictionsPenalties extends AbstractSection implements TransportManagerSectionInterface
{

    use SectionSerializeTrait;

    private $convictions;

    public function populate(array $transportManagerApplication)
    {
        $convictions = $transportManagerApplication['transportManager']['previousConvictions'];
        $template = 'markup-' . $this->getTranslationTemplate() . "answer-convictions";
        foreach ($convictions as $conviction) {
            $this->convictions[] = $this->populateTemplate(
                $template,
                [
                    $conviction['categoryText'],
                    $conviction['convictionDate']
                ]
            );
        }
        empty($this->convictions) ? $this->convictions = "None added":null;
        return $this;
    }
}