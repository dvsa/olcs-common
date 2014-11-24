<?php

/**
 * Publication info filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

use Common\Exception\ResourceNotFoundException;

/**
 * Publication info filter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Publication extends AbstractPublicationFilter
{
    const NEW_STATUS = 'pub_s_new';

    /**
     * @param \Zend\Stdlib\ArrayObject $publication
     * @return \Zend\Stdlib\ArrayObject
     * @throws ResourceNotFoundException
     */
    public function filter($publication)
    {
        $params = [
            'pubType' => $publication->offsetGet('pubType'),
            'trafficArea' => $publication->offsetGet('trafficArea'),
            'pubStatus' => self::NEW_STATUS
        ];

        $data = $this->getServiceLocator()->get('\Common\Service\Data\Publication')->fetchPublicationData($params);

        if (!isset($data['Results'][0]['id'])) {
            throw new ResourceNotFoundException('No publication found');
        }

        $newData = [
            'publication' => $data['Results'][0]['id'],
            'publicationNo' => $data['Results'][0]['publicationNo'],
            'origPubDate' => $data['Results'][0]['pubDate']
        ];

        $publication = $this->mergeData($publication, $newData);

        return $publication;
    }
}
