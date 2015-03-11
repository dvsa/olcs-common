<?php

/**
 * Transport Manager Decision text 1 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * TM Decision text 1 filter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class TmDecisionText1 extends Text1
{
    protected $pi = 'TM Public Inquiry (Case ID: %s, Public Inquiry ID: %s) for %s held at %s,
    on %s at %s';

    /**
     * @param \Common\Data\Object\Publication  $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $hearingData = $publication->offsetGet('hearingData');

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
        $case = $publication->offsetGet('case');
        $transportManagerName = $publication->offsetGet('transportManagerName');

        return sprintf(
            $this->pi,
            $case['id'],
            $hearingData['id'],
            $transportManagerName,
            $hearingData['piVenueOther'],
            $hearingData['date'],
            $hearingData['time'],
            ''
        );
    }
}
