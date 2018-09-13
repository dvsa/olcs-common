<?php


namespace Common\Data\Mapper\Lva\TransportManager\Sections;


class AdditionalInformation extends AbstractSection implements TransportManagerSectionInterface
{
    use SectionSerializeTrait;

    private $additionalInformation;

    private $files;

    public function populate(array $transportManagerApplication)
    {
        $files = 0;
        $additionalInfo = $transportManagerApplication['additionalInformation'];
        $this->additionalInformation = empty($additionalInfo) ? 'None Added' : 'Details added';
        $documents = $transportManagerApplication['transportManager']['documents'];
        foreach ($documents as $document) {
            if ($document['category']['id'] === \Common\Category::CATEGORY_TRANSPORT_MANAGER &&
                $document['subCategory']['id'] ===
                \Common\Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL &&
                $document['application']['id'] === $transportManagerApplication['application']['id']
            ) {
                $files ++;
            }
        }
        $this->files = $files;
        return $this;
    }
}
