<?php

/**
 * Previous publication filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

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
        $params = $this->getParams($publication);

        $publicationNo = $publication->offsetGet('publicationNo');
        $previousPublications = [];

        $data = $this->getServiceLocator()
            ->get('DataServiceManager')
            ->get('Common\Service\Data\PublicationLink')
            ->fetchList($params);

        //not possible to get what we need from the current API,
        //but there will never be more than a few records to sort through
        foreach ($data['Results'] as $record) {
            if ($record['publication']['publicationNo'] < $publicationNo) {
                $previousPublications[] = $record['publication']['publicationNo'];
            }
        }

        if (!empty($previousPublications)) {
            arsort($previousPublications, SORT_NUMERIC);
            $publication = $this->setPreviousPublication($publication, reset($previousPublications));
        }

        return $publication;
    }

    /**
     * @param \Common\Data\Object\Publication $publication
     * @param array $previousPublications
     * @return \Common\Data\Object\Publication
     */
    public function setPreviousPublication($publication, $previousPublications)
    {
        $hearingData = $publication->offsetGet('hearingData');
        $hearingData['previousPublication'] = $previousPublications;
        $publication->offsetSet('hearingData', $hearingData);

        return $publication;
    }

    /**
     * Gets parameters needed to check whether a
     *
     * @param $publication
     * @return array
     */
    public function getParams($publication)
    {
        return [
            'pubType' => $publication->offsetGet('pubType'),
            'trafficArea' => $publication->offsetGet('trafficArea'),
            'pi' => $publication->offsetGet('pi'),
            'limit' => 'all'
        ];
    }
}
