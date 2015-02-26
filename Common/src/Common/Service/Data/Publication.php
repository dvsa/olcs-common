<?php

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Common\Util\RestClient;
use Common\Exception\ResourceNotFoundException;
use Common\Exception\DataServiceException;

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Publication extends Generic
{
    protected $serviceName = 'Publication';

    protected $newStatus = 'pub_s_new';
    protected $generatedStatus = 'pub_s_generated';
    protected $printedStatus = 'pub_s_printed';

    public function publish($id)
    {
        $bundle = [
            'children' => [
                'pubStatus' => []
            ],
        ];

        $currentPublication = $this->fetchOne($id, $bundle);

        //check publication exists
        if (!isset($currentPublication['id'])) {
            throw new ResourceNotFoundException('Publication not found');
        }

        //check status is correct
        if ($currentPublication['pubStatus']['id'] != $this->generatedStatus) {
            throw new DataServiceException('Only publications with status of Generated may be published');
        }

        //set the publication to generated
        $data = [
            'id' => $id,
            'pubStatus' => $this->printedStatus,
            'version' => $currentPublication['version']
        ];

        return $this->save($data);
    }

    public function generate($id)
    {
        $bundle = [
            'children' => [
                'trafficArea' => [],
                'pubStatus' => []
            ],
        ];

        $currentPublication = $this->fetchOne($id, $bundle);

        //check publication exists
        if (!isset($currentPublication['id'])) {
            throw new ResourceNotFoundException('Publication not found');
        }

        //check status is correct
        if ($currentPublication['pubStatus']['id'] != $this->newStatus) {
            throw new DataServiceException('Only publications with status of New may be generated');
        }

        //set the publication to generated
        $data = [
            'id' => $id,
            'pubStatus' => $this->generatedStatus,
            'version' => $currentPublication['version']
        ];

        $this->save($data);

        //create new publication, same as the old one but with incremented pubNo and pubDate
        $newPublication = [
            'trafficArea' => $currentPublication['trafficArea']['id'],
            'pubStatus' => $this->newStatus,
            'pubDate' => $this->getNewPublicationDateFromPrevious($currentPublication['pubDate']),
            'pubType' => $currentPublication['pubType'],
            'publicationNo' => $currentPublication['publicationNo'] + 1
        ];

        return $this->save($newPublication);
    }

    private function getNewPublicationDateFromPrevious($previousDate)
    {
        $date = new \DateTime($previousDate);
        $date->add(new \DateInterval('P14D'));

        return $date->format('Y-m-d');
    }
}
