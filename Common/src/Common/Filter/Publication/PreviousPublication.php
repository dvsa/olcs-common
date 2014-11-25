<?php

/**
 * Previous publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Previous publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousPublication extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $params = [
            'pubType' => $publication->offsetGet('pubType'),
            'trafficArea' => $publication->offsetGet('trafficArea'),
            'pi' => $publication->offsetGet('pi'),
            'limit' => 1000
        ];

        $publicationNo = $publication->offsetGet('publicationNo');
        $previousPublications = [];

        $data = $this->getServiceLocator()
            ->get('\Common\Service\Data\PublicationLink')
            ->fetchPublicationLinkData($params);

        //not possible to get what we need from the current API,
        //but there will never be more than a few records to sort through
        foreach ($data['Results'] as $record) {
            if ($record['publication']['publicationNo'] < $publicationNo) {
                $previousPublications[] = $record['publication']['publicationNo'];
            }
        }

        if (!empty($previousPublications)) {
            arsort($previousPublications, SORT_NUMERIC);
            $hearingData = $publication->offsetGet('hearingData');
            $hearingData['previousPublication'] = reset($previousPublications);
            $publication->offsetSet('hearingData', $hearingData);
        }

        return $publication;
    }
}
