<?php

/**
 * Application Text 2 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Application Text 2 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationText2 extends Text1
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');
        $licType = $publication->offsetGet('licType');
        $publicationSection = $publication->offsetGet('publicationSection');

        $text = [];

        //licence cancellation
        if ($publication->offsetExists('licenceCancelled')) {
            if ($licType == self::PSV_LIC_TYPE) { //PSV licence cancellation
                $text = $this->getPsvCancelled($publication, $licenceData, $text);
            } else {
                $text = $this->getGvCancelled($publication, $licenceData, $text);
            }
        } elseif ($licType == self::GV_LIC_TYPE) { //non cancellation GV
            switch ($publicationSection) {
                case self::APP_GRANTED_SECTION:
                    $text = $this->getAllData($licenceData, $text);
                    break;
                case self::APP_WITHDRAWN_SECTION:
                    $text[] = $this->getLicenceInfo($licenceData);
                    break;
                case self::APP_REFUSED_SECTION:
                default:
                    $text = $this->getAllData($licenceData, $text);
                    break;
            }
        } else { //non cancellation PSV
            $text = $this->getAllData($licenceData, $text);
        }

        $newData = [
            'text2' => implode("\n", $text)
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }

    /**
     * @param $licenceData
     * @param $text
     * @return array
     */
    public function getAllData($licenceData, $text) {
        $text[] = $this->getLicenceInfo($licenceData);
        $text[] = $this->getPersonInfo($licenceData['organisation']);

        return $text;
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @param $licenceData
     * @param $text
     * @return array
     */
    public function getGvCancelled($publication, $licenceData, $text) {
        $text[] = $publication->offsetGet('licenceCancelled');
        $text[] = $this->getLicenceInfo($licenceData) . "\n";

        return $text;
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @param $licenceData
     * @param $text
     * @return array
     */
    public function getPsvCancelled($publication, $licenceData, $text) {
        $text = $this->getGvCancelled($publication, $licenceData, $text);
        $text[] = $this->getPersonInfo($licenceData['organisation']);

        return $text;
    }

    /**
     * @param array $licenceData
     * @return string
     */
    public function getLicenceInfo($licenceData)
    {
        $licence = $licenceData['organisation']['name'];

        if (!empty($licenceData['organisation']['tradingNames'])) {
            $latestTradingName = end($licenceData['organisation']['tradingNames']);
            $licence .= ' ' . sprintf($this->tradingAs, $latestTradingName['name']);
        }

        return strtoupper($licence);
    }
}
