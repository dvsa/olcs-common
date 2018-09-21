<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;

class RevokedLicences extends AbstractSection implements TransportManagerSectionInterface
{
    use SectionSerializeTrait;

    private $revokedLicences;

    public function populate(array $transportManagerApplication)
    {

        $revokedLicences = $transportManagerApplication['transportManager']['otherLicences'];

        if (empty($revokedLicences)) {
            $this->revokedLicences = 'None added';
            return $this;
        }

        $licences = $this->sortByCreated($revokedLicences);

        foreach ($licences as $licence) {
            if (!empty($licence['licNo'])) {
                $this->revokedLicences[] = $licence['licNo'];
            }
        }
        $template = 'markup-' . $this->getTranslationTemplate() . "answer-revokedLicences";
        $this->revokedLicences = $this->populateTemplate($template, $this->revokedLicences);
        return $this;
    }
}
