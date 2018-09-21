<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


class OtherEmployment extends AbstractSection implements TransportManagerSectionInterface
{

    use SectionSerializeTrait;

    private $employments;

    public function populate(array $transportManagerApplication)
    {
        $employmentData = [];
        $employments = $transportManagerApplication['transportManager']['employments'];
        $employments = $this->sortByCreated($employments);
        $noOfPreviousRoles = count($employments);

        for ($x = 0; ($x < $noOfPreviousRoles) && ($x < 3); $x++) {
            $employmentData[] = $employments[$x]['employerName'];

        }


        $this->employments = $noOfPreviousRoles > 0 ? $this->format($employmentData, $noOfPreviousRoles) : 'None Added';
        return $this;
    }

    private function format(array $employments, $noOfPreviousRoles): string
    {
        $suffix = '';
        if ($noOfPreviousRoles > 3) {
            $suffix = sprintf('<span>and %d more</span>', $noOfPreviousRoles);
        }

        $template = 'markup-' . $this->getTranslationTemplate() . "-answer-otherEmployments";
        return $this->populateTemplate($template, $employments) . $suffix;
    }
}

