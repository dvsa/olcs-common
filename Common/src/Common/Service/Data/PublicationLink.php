<?php

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Common\Exception\DataServiceException;
use Common\Exception\ResourceNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Data\Object\Publication as PublicationObject;
use Zend\Stdlib\ArrayObject;

/**
 * Publication Link service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationLink extends AbstractData implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const NEW_PUBLICATION_STATUS = 'pub_s_new';

    /**
     * @var string
     */
    protected $serviceName = 'PublicationLink';

    /**
     * @return \Common\Data\Object\Publication
     */
    public function createEmpty()
    {
        return new PublicationObject();
    }

    /**
     * @param int $id
     * @param bool $checkStatus
     * @return array
     * @throws ResourceNotFoundException
     * @throws DataServiceException
     */
    public function delete($id, $checkStatus = true)
    {
        if ($checkStatus) {
            $params = ['id' => $id];
            $existing = $this->fetchList($params);

            if (empty($existing)) {
                throw new ResourceNotFoundException('Publication record could not be found');
            }

            if ($existing['publication']['pubStatus']['id'] != self::NEW_PUBLICATION_STATUS) {
                throw new DataServiceException('Only unpublished entries may be deleted');
            };
        }

        return parent::delete($id);
    }

    /**
     * @param ArrayObject $dataObject
     * @return int
     */
    public function save(ArrayObject $dataObject)
    {
        $publicationLinkId = parent::save($dataObject);
        $this->savePoliceData($dataObject, $publicationLinkId);

        return $publicationLinkId;
    }

    /**
     * Calls the police data service to save related police data
     *
     * @param $dataObject
     * @param int $publicationLinkId
     *
     * @return bool
     */
    public function savePoliceData($dataObject, $publicationLinkId)
    {
        $policeData = $dataObject->offsetGet('policeData');

        //there isn't a person id in the schema and Paul doesn't want to add one.
        //So we're deleting and replacing any existing data.
        $policeDataService = $this->getServiceLocator()->get('Common\Service\Data\PublicationPolice');
        $policeDataService->deleteList(['publicationLink' => $publicationLinkId]);

        if (!empty($policeData)) {
            $policeDataObject = $policeDataService->createEmpty();

            foreach ($policeData as $data) {
                $policeDataObject->offsetSet('publicationLink', $publicationLinkId);
                $policeDataObject->offsetSet('forename', $data['forename']);
                $policeDataObject->offsetSet('familyName', $data['familyName']);
                $policeDataObject->offsetSet('birthDate', $data['birthDate']);
                $policeDataService->save($policeDataObject);
            }
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getBundle()
    {
        return [
            'properties' => 'ALL',
            'children' => [
                'publication' => [
                    'properties' => 'ALL',
                    'children' => [
                        'pubStatus' => [
                            'properties' => 'ALL'
                        ],
                        'trafficArea' => [
                            'properties' => 'ALL'
                        ]
                    ]
                ],
                'publicationSection' => [
                    'properties' => 'ALL'
                ]
            ]
        ];
    }
}
