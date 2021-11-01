<?php

namespace Common\Data\Mapper\Lva\TransportManager\Sections;

use Common\Category;

class Details extends AbstractSection
{
    use SectionSerializeTrait;

    private $name;

    private $birthDate;

    private $birthPlace;

    private $emailAddress;

    private $certificate;

    private $hasUndertakenTraining;

    private $homeCd;

    private $workCd;

    /**
     * populate
     *
     * @param array $transportManagerApplication
     *
     * @return \Object;
     */
    public function populate(array $transportManagerApplication)
    {
        $person = $transportManagerApplication['transportManager']['homeCd']['person'];
        $this->populatePersonDetails($person);
        $this->emailAddress = $transportManagerApplication['transportManager']['homeCd']['emailAddress'];
        $this->certificate = $this->processDocuments($transportManagerApplication);
        $this->hasUndertakenTraining = $transportManagerApplication['hasUndertakenTraining'];

        foreach (['homeCd', 'workCd'] as $addresses) {
            $address = $this->processAddress($transportManagerApplication['transportManager'][$addresses]['address']);
            $address = $this->populateTemplate(
                'markup-' . $this->getTranslationTemplate() . "answer-address",
                $address
            );
            $this->birthDate = (new \DateTime($this->birthDate))->format('d M Y');
            $this->$addresses = $address;
        }

        return $this;
    }

    /**
     * populatePersonDetails
     *
     * @param $person
     */
    private function populatePersonDetails($person): void
    {
        $this->name = $person['forename'] . " " . $person['familyName'];
        $props = array_keys(get_object_vars($this));
        foreach ($props as $property) {
            if (array_key_exists($property, $person)) {
                $this->$property = $person[$property];
            }
        }
    }

    /**
     * processDocuments
     *
     * @param array $transportManagerApplication
     *
     * @return mixed
     */
    private function processDocuments(array $transportManagerApplication)
    {
        $hasDocument = false;
        $documents = $transportManagerApplication['transportManager']['documents'];
        foreach ($documents as $document) {
            if ($document['category']['id'] === Category::CATEGORY_TRANSPORT_MANAGER &&
                $document['subCategory']['id'] === Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CPC_OR_EXEMPTION
            ) {
                $hasDocument = true;
            }
        }

        return $hasDocument ? "Certificate Added" : "No certificates attached";
    }

    /**
     * processAddress
     *
     * @param $data
     *
     * @return array
     */
    private function processAddress($data): array
    {
        $formattedAddress = [];
        foreach ($data as $key => $value) {
            if (strpos($key, 'address') !== false || in_array($key, ['postcode', 'town'])) {
                $formattedAddress[$key] = $value;
            }
        }
        $formattedAddress['country'] = $data['countryCode']['countryDesc'];

        $formattedAddress = array_diff($formattedAddress, array(''))
            + array_intersect($formattedAddress, array(''));
        return $formattedAddress;
    }
}
