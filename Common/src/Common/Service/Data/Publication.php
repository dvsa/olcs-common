<?php

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Dvsa\Olcs\Transfer\Command\Document\GenerateAndStore;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\Util\RestClient;
use Common\Exception\ResourceNotFoundException;
use Common\Exception\DataServiceException;

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Publication extends Generic implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $serviceName = 'Publication';

    protected $newStatus = 'pub_s_new';
    protected $generatedStatus = 'pub_s_generated';
    protected $printedStatus = 'pub_s_printed';

    /**
     * Publishes a publication document
     *
     * @param int $id
     * @return int
     * @throws DataServiceException
     * @throws ResourceNotFoundException
     * @throws \Common\Exception\BadRequestException
     */
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

    /**
     * Generates a publication document
     *
     * @param int $id
     * @return int
     * @throws DataServiceException
     * @throws ResourceNotFoundException
     * @throws \Common\Exception\BadRequestException
     */
    public function generate($id)
    {
        $currentPublication = $this->fetchOne($id, $this->getGenerateBundle());

        //check publication exists
        if (!isset($currentPublication['id'])) {
            throw new ResourceNotFoundException('Publication not found');
        }

        //check status is correct
        if ($currentPublication['pubStatus']['id'] != $this->newStatus) {
            throw new DataServiceException('Only publications with status of New may be generated');
        }

        $documentId = $this->createPublicationDocumentRecord($currentPublication);

        //set the publication to generated
        $data = [
            'id' => $id,
            'pubStatus' => $this->generatedStatus,
            'version' => $currentPublication['version'],
            'document' => $documentId
        ];

        $this->save($data);

        //create new publication, same as the old one but with incremented pubNo and pubDate
        $newPublication = [
            'trafficArea' => $currentPublication['trafficArea']['id'],
            'pubStatus' => $this->newStatus,
            'pubDate' => $this->getNewPublicationDateFromPrevious($currentPublication['pubDate']),
            'pubType' => $currentPublication['pubType'],
            'publicationNo' => $currentPublication['publicationNo'] + 1,
            'docTemplate' => $currentPublication['docTemplate']['id']
        ];

        return $this->save($newPublication);
    }

    /**
     * @param string $locale
     * @param string $docTypeName
     * @param int $year
     * @param int $month
     * @param string $status
     * @return array
     */
    public function getFilePathVariables($locale, $docTypeName, $year, $month, $status)
    {
        return [
            'locale' => $locale,
            'doc_type_name' => $docTypeName,
            'year' => $year,
            'month' => $month,
            'status' => $status
        ];
    }

    /**
     * Creates a publication document record
     *
     * @param array $currentPublication
     * @return mixed
     */
    private function createPublicationDocumentRecord($currentPublication)
    {
        // @NOTE Tmp doc generation solution until section is migrated

        $description = $currentPublication['docTemplate']['description'];

        switch ($currentPublication['pubStatus']['id']) {
            case 'pub_s_new':
                $description .= 'generated';
                break;
            case 'pub_s_generated':
                $description .= 'printed';
                break;
        }

        $dtoData = [
            'template' => $currentPublication['docTemplate']['document']['identifier'],
            'query' => [
                'publicationId' => $currentPublication['id'],
                'pubType' => $currentPublication['pubType']
            ],
            'description'   => $description,
            'category'      => $currentPublication['docTemplate']['category']['id'],
            'subCategory'   => $currentPublication['docTemplate']['subCategory']['id'],
            'isExternal'    => true,
            'isReadOnly'    => true
        ];

        $response = $this->handleCommand(GenerateAndStore::create($dtoData));

        $result = $response->getResult();

        return $result['id']['document'];

    }

    /**
     * @NOTE Tmp code until section migrated
     */
    private function handleCommand($command)
    {
        $annotationBuilder = $this->getServiceLocator()->get('TransferAnnotationBuilder');
        $commandService = $this->getServiceLocator()->get('CommandService');

        $command = $annotationBuilder->createCommand($command);
        return $commandService->send($command);
    }

    /**
     * Bundle for generate action
     *
     * @return array
     */
    private function getGenerateBundle()
    {
        return [
            'children' => [
                'trafficArea' => [],
                'pubStatus' => [],
                'docTemplate' => [
                    'children' => [
                        'document' => [],
                        'category',
                        'subCategory'
                    ]
                ]
            ],
        ];
    }

    /**
     * Calculates the publication date of the next publication
     *
     * @param string $previousDate
     * @return string
     */
    private function getNewPublicationDateFromPrevious($previousDate)
    {
        $date = new \DateTime($previousDate);
        $date->add(new \DateInterval('P14D'));

        return $date->format('Y-m-d');
    }
}
