<?php

/**
 * Previous unpublished filter, gets the ID of unpublished records matching the same section id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Filter\Publication;

/**
 * Previous unpublished filter, gets the ID of unpublished records matching the same section id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PreviousUnpublished extends AbstractPublicationFilter
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
            'pi' => $publication->offsetGet('pi'),
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
