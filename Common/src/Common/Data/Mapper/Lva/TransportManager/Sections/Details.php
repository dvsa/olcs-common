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

    private $homeCd;

    private $workCd;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setName
     *
     * @param $person
     */
    public function setName($person): void
    {
        $name = $person['forename'] . " " . $person['familyName'];
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param mixed $birthDate
     */
    public function setBirthDate($birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    /**
     * @return mixed
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * @param mixed $birthPlace
     */
    public function setBirthPlace($birthPlace): void
    {
        $this->birthPlace = $birthPlace;
    }

    /**
     * @return mixed
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @param mixed $emailAddress
     */
    public function setEmailAddress($emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param mixed $certificate
     */
    public function setCertificate($certificate): void
    {
        $this->certificate = $certificate;
    }

    /**
     * @return mixed
     */
    public function getHomeCd()
    {
        return $this->homeCd;
    }

    /**
     * @param mixed $homeCd
     */
    public function setHomeCd($homeCd): void
    {
        $this->homeCd = $homeCd;
    }

    /**
     * @return mixed
     */
    public function getWorkCd()
    {
        return $this->workCd;
    }

    /**
     * @param mixed $workCd
     */
    public function setWorkCd($workCd): void
    {
        $this->workCd = $workCd;
    }


    /**
     * populate
     *
     * @param array $transportManagerApplication
     *
     *
     * @return \Object;
     */
    public function populate(array $transportManagerApplication)
    {
        $person = $transportManagerApplication['transportManager']['homeCd']['person'];
        $this->populatePersonDetails($person);
        $this->setEmailAddress($transportManagerApplication['transportManager']['homeCd']['emailAddress']);
        $this->setCertificate($this->processDocuments($transportManagerApplication));

        foreach (['homeCd', 'workCd'] as $addresses) {
            $address = $this->processAddress($transportManagerApplication['transportManager'][$addresses]['address']);

           // $address = $this->populateTemplate($this->getTranslationTemplate() . "addressMarkup", $address);
            $this->{'set' . ucfirst($addresses)}($address);
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
        $this->setName($person);
        $props = array_keys(get_object_vars($this));
        foreach ($props as $property) {
            if (array_key_exists($property, $person)) {
                $this->{'set' . ucfirst($property)}($person[$property]);
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
        return $formattedAddress;
    }
}
