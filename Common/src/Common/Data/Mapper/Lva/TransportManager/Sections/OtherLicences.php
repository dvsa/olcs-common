<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


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

        if (count($licences) > 1) {
            usort($this->licences, function ($a, $b) {
                return strtotime($b['createdOn']) - strtotime($a['createdOn']);
            });
        }

        foreach ($licences as $licence) {
            $this->licences[] = $licence['licNo'];
        }
        $template = $this->getTranslationTemplate() . "-otherLicences-answer";
        //$this->licences = populateTemplate($template, $this->licences);
        return $this;
    }
}
