<?php

/**
 * Text 1 filter, extended by hearing and decision text1 filters
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Text 1 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text1 extends AbstractPublicationFilter
{
    protected $previousPublication = '(Previous Publication:(%s))';
    protected $previousHearingAdjourned = 'Previous hearing on %s was adjourned.';
    protected $tradingAs = 'T/A %s';
    protected $orgTypeLtd = 'org_t_rc';
    protected $orgTypeLlp = 'org_t_llp';
    protected $pi = '';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
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

        //previous hearing, only present on hearing publication, not on decision
        if (isset($hearingData['previousHearing']) && $hearingData['previousHearing']['isAdjourned']) {
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
     * @param \Common\Data\Object\Publication $publication
     * @param array $hearingData
     * @return string
     */
    public function getOpeningText($publication, $hearingData)
    {
        return sprintf(
            $this->pi,
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
        return sprintf($this->previousPublication, $hearingData['previousPublication']);
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
            $licence .= "\n" . sprintf($this->tradingAs, $latestTradingName['name']);
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
            case $this->orgTypeLtd:
                $prefix = 'Director(s): ';
                break;
            case $this->orgTypeLlp:
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

    /**
     * @param array $hearingData
     * @return string
     */
    public function getPreviousHearing($hearingData)
    {
        return sprintf($this->previousHearingAdjourned, $hearingData['previousHearing']['date']);
    }
}
