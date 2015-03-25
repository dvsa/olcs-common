<?php

/**
 * Bus Registration Text 2 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Bus Registration Text 2 filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusRegText2 extends AbstractPublicationFilter
{
    protected $tradingAs = 'T/A %s';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $licenceData = $publication->offsetGet('licenceData');

        $licence = $licenceData['organisation']['name'];

        if (!empty($licenceData['organisation']['tradingNames'])) {
            $latestTradingName = end($licenceData['organisation']['tradingNames']);
            $licence .= " " . sprintf($this->tradingAs, $latestTradingName['name']);
        }

        //licence address
        if ($publication->offsetExists('licenceAddress')) {
            $licence .= ", " . $publication->offsetGet('licenceAddress');
        }

        $newData = [
            'text2' => strtoupper($licence),
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
