<?php

/**
 * Hearing text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Hearing text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingText1 extends AbstractPublicationFilter
{
    const PI = 'Public Inquiry (%s) to be held at %s, on %s commencing at %s';
    const PREVIOUS_PUBLICATION = '(Previous Publication:(%s))';
    const PREVIOUS_HEARING_ADJOURNED = 'Previous hearing on %s was adjourned.';
    const TRADING_AS = 'T/A %s';
    const ORG_TYPE_LTD = 'org_t_rc';
    const ORG_TYPE_LLP = 'org_t_llp';

    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $hearingData = $publication->offsetGet('hearingData');
        $licenceData = $publication->offsetGet('licenceData');

        $hearingText = [];
        $hearingText[] = $this->getOpeningText($publication, $hearingData);

        //previous publication
        if (isset($hearingData['previousPublication'])) {
            $hearingText[] = $this->getPreviousPublication($hearingData);
        }

        //previous hearing
        if (isset($hearingData['previousHearing'])) {
            $hearingText[] = $this->getPreviousHearing($hearingData);
        }

        //licence info
        $hearingText[] = $this->getLicenceInfo($licenceData);

        //person data
        $hearingText[] = $this->getPersonInfo($licenceData['organisation']);

        //licence address
        if ($publication->offsetExists('licenceAddress')) {
            $hearingText[] = "\n" . strtoupper($publication->offsetGet('licenceAddress'));
        }

        $publication->offsetSet('text1', implode(' ', $hearingText));

        return $publication;
    }

    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @param array $hearingData
     * @return string
     */
    public function getOpeningText($publication, $hearingData)
    {
        return sprintf(
            self::PI,
            $publication->offsetGet('pi'),
            $hearingData['piVenueOther'],
            $hearingData['date'],
            $hearingData['time']
        );
    }

    /**
     * @param array $hearingData
     * @return string
     */
    public function getPreviousPublication($hearingData)
    {
        return sprintf(self::PREVIOUS_PUBLICATION, $hearingData['previousPublication']);
    }

    /**
     * @param array $hearingData
     * @return string
     */
    public function getPreviousHearing($hearingData)
    {
        if ($hearingData['previousHearing']['isAdjourned']) {
            return sprintf(self::PREVIOUS_HEARING_ADJOURNED, $hearingData['previousHearing']['date']);
        }
    }

    /**
     * @param array $licenceData
     * @return string
     */
    public function getLicenceInfo($licenceData)
    {
        $licence = "\n" . sprintf(
            '%s %s '. "\n" . '%s',
            $licenceData['licNo'],
            $licenceData['licenceType']['olbsKey'],
            $licenceData['organisation']['name']
        );

        if (!empty($licenceData['organisation']['tradingNames'])) {
            $latestTradingName = end($licenceData['organisation']['tradingNames']);
            $licence .= "\n" . sprintf(self::TRADING_AS, $latestTradingName['name']);
        }

        return strtoupper($licence);
    }

    /**
     * @param array $organisationData
     * @return string
     */
    public function getPersonInfo($organisationData)
    {
        $personData = $organisationData['organisationPersons'];
        $persons = [];

        switch ($organisationData['type']['id']) {
            case self::ORG_TYPE_LTD:
                $prefix = 'Director(s): ';
                break;
            case self::ORG_TYPE_LLP:
                $prefix = 'Partner(s): ';
                break;
            default:
                $prefix = '';
        }

        foreach ($personData as $person) {
            $persons[] = strtoupper(sprintf('%s %s', $person['person']['forename'], $person['person']['familyName']));
        }

        return "\n" . $prefix . implode(', ', $persons);
    }
}
