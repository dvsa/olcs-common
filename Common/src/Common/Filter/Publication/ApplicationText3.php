<?php

/**
 * Application Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Application Text 3 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText3 extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licType = $publication->offsetGet('licType');
        $publicationSection = $publication->offsetGet('publicationSection');

        $text = [];

        //GV
        if ($licType == self::GV_LIC_TYPE) {
            if ($publication->offsetExists('licenceCancelled')) {
                $text = $this->getPartialData($publication, $text);
            } else {
                switch ($publicationSection) {
                    case self::APP_GRANTED_SECTION:
                        $text = $this->getPartialData($publication, $text);
                        break;
                    case self::APP_WITHDRAWN_SECTION:
                    case self::APP_REFUSED_SECTION:
                        $text = $this->getPartialData($publication, $text);
                        break;
                    default:
                        $text = $this->getAllData($publication, $text);
                        break;
                }
            }
        } else {
            //PSV
            $text = $this->getAllData($publication, $text);
        }

        $newData = [
            'text3' => implode("\n", $text)
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }

    public function getAllData($publication, $text)
    {
        $text = $this->addLicenceAddress($publication, $text);
        $text = $this->addBusNote($publication, $text);
        $text = $this->getPartialData($publication, $text);

        return $text;
    }

    public function getPartialData($publication, $text)
    {
        $text = $this->addOcDetails($publication, $text);
        $text = $this->addConditionUndertaking($publication, $text);

        return $text;
    }

    /**
     * Adds oc details, including authorisation and tm
     *
     * @param \Common\Data\Object\Publication $publication
     * @param array $text
     * @return array
     */
    public function addOcDetails($publication, $text)
    {
        //operating centre address
        if ($publication->offsetExists('operatingCentres')) {
            foreach ($publication->offsetGet('operatingCentres') as $oc) {
                $text[] = $oc;
            }
        }

        //Transport Managers
        if ($publication->offsetExists('transportManagers')) {
            $text[] = 'Transport Manager(s): ' . $publication->offsetGet('transportManagers');
        }

        return $text;
    }

    /**
     * Adds condition and undertaking
     *
     * @param \Common\Data\Object\Publication $publication
     * @param array $text
     * @return array
     */
    public function addConditionUndertaking($publication, $text)
    {
        //conditions and undertakings
        $conditionUndertaking = $publication->offsetGet('conditionUndertaking');

        foreach ($conditionUndertaking as $cuString) {
            $text[] = $cuString;
        }

        return $text;
    }

    /**
     * Adds licence address
     *
     * @param \Common\Data\Object\Publication $publication
     * @param array $text
     * @return array
     */
    public function addLicenceAddress($publication, $text)
    {
        $text[] = $publication->offsetGet('licenceAddress');
        return $text;
    }

    /**
     * If we have a bus note, add it in
     *
     * @param \Common\Data\Object\Publication $publication
     * @param array $text
     * @return array
     */
    public function addBusNote($publication, $text)
    {
        if ($publication->offsetExists('busNote')) {
            $text[] = $publication->offsetGet('busNote');
        }

        return $text;
    }
}
