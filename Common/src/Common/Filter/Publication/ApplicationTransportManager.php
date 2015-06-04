<?php

/**
 * Application Transport Manager filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Application Transport Manager filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ApplicationTransportManager extends AbstractPublicationFilter
{
    const GV_LIC_TYPE = 'lcat_gv';

    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $tmData = $publication->offsetGet('transportManagerData');
        $newTmData = [];

        foreach ($tmData as $tm) {
            $forename = (string)$tm['transportManager']['homeCd']['person']['forename'];
            $familyName = (string)$tm['transportManager']['homeCd']['person']['familyName'];

            $newTmData[] = trim($forename . ' ' . $familyName);
        }

        if (!empty($newTmData)) {
            $transportManagers = implode(', ', $newTmData);

            $newData = [
                'transportManagers' => $transportManagers
            ];

            $publication = $this->mergeData($publication, $newData);
        }

        return $publication;
    }
}
