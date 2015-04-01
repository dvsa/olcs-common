<?php

/**
 * Previous unpublished filter for bus reg,
 * gets the ID of unpublished records for a bus reg matching the same section id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Previous unpublished filter for bus reg,
 * gets the ID of unpublished records for a bus reg matching the same section id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousUnpublishedBus extends AbstractPublicationFilter
{
    /**
     * @param \Common\Data\Object\Publication $publication
     * @return \Common\Data\Object\Publication
     */
    public function filter($publication)
    {
        $params = [
            'publication' => $publication->offsetGet('publication'),
            'publicationSection' => $publication->offsetGet('publicationSection'),
            'busReg' => $publication->offsetGet('busReg'),
            'sort' => 'id',
            'order' => 'DESC'
        ];

        $data = $this->getServiceLocator()
            ->get('DataServiceManager')
            ->get('\Common\Service\Data\PublicationLink')
            ->fetchList($params);

        if (isset($data['Results'])) {
            foreach ($data['Results'] as $result) {
                if ($result['publication']['pubStatus']['id'] == $this->publicationNewStatus) {
                    $newData = [
                        'id' => $result['id'],
                        'version' => $result['version']
                    ];

                    $publication = $this->mergeData($publication, $newData);
                    break;
                }
            }
        }

        return $publication;
    }
}
