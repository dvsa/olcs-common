<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


/**
 * Class OtherLicences
 *
 * @package Common\Data\Mapper\Lva\TransportManager\Sections
 */
class OtherLicences extends AbstractSection implements TransportManagerSectionInterface
{

    use SectionSerializeTrait;

    private $licences;

    public function populate(array $transportManagerApplication)
    {
        $licences = $transportManagerApplication['transportManager']['otherLicences'];
        if (empty($licences)) {
            $this->licences = 'None Added';
            return $this;
        }

        $licences = $this->sortByCreated($licences);

        foreach ($licences as $licence) {
            $this->licences[] = $licence['licNo'];
        }
        $template = 'markup-'.$this->getTranslationTemplate() . "-otherLicences-answer";
        $this->licences = populateTemplate($template, $this->licences);
        return $this;
    }
}
