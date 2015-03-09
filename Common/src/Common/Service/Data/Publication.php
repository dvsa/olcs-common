<?php

/**
 * Publication service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

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

        $storedFile = $this->createPublicationDocument($currentPublication);
        $documentId = $this->createPublicationDocumentRecord($storedFile, $currentPublication);

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

    public function getPublicationFilename($publication)
    {
        $date = new \DateTime($publication['pubDate']);

        return $publication['trafficArea']['name']
        . $publication['pubType']
        . $date->format('Y')
        . $date->format('m')
        . $date->format('d')
        . date($this->getDocumentService()->getTimestampFormat())
        . '.rtf';
    }

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

    public function getFilePathVariablesFromPublication($publication)
    {
        $locale = ($publication['trafficArea']['isNi'] == 'Y' ? 'ni' : 'gb');
        $date = new \DateTime($publication['pubDate']);

        return $this->getFilePathVariables(
            $locale,
            'publications',
            $date->format('Y'),
            $date->format('m'),
            strtolower($publication['pubStatus']['description'])
        );
    }

    private function createPublicationDocumentRecord($storedFile, $currentPublication)
    {
        $fileName = $this->getDocumentService()->formatFilename($this->getPublicationFilename($currentPublication));

        $templateData = [
            'identifier'    => $storedFile->getIdentifier(),
            'description'   => $currentPublication['docTemplate']['description'],
            'filename'      => $fileName,
            'fileExtension' => 'doc_rtf',
            'category'      => $currentPublication['docTemplate']['category']['id'],
            'subCategory'   => $currentPublication['docTemplate']['subCategory']['id'],
            'isDigital'     => true,
            'isReadOnly'    => true,
            'issuedDate'    => date('Y-m-d H:i:s'),
            'size'          => $storedFile->getSize()
        ];

        return $this->getDocumentDataService()->save($templateData);
    }

    private function createPublicationDocument($publication)
    {
        $queryData = [
            'user' => 1,
            'publicationId' => $publication['id'],
            'pubType' => $publication['pubType']
        ];

        //get the template
        $file = $this->getContentStore()->read($publication['docTemplate']['document']['identifier']);

        //work out the query to be sent
        $query = $this->getDocumentService()->getBookmarkQueries(
            $file,
            $queryData
        );

        $restClient = $this->getServiceLocator()->get('Helper\Rest');
        $result = $restClient->makeRestCall('BookmarkSearch', 'GET', [], $query);

        //populate the bookmarks
        $content = $this->getDocumentService()->populateBookmarks(
            $file,
            $result
        );

        $details = json_encode(
            [
                'details' => [],
                'bookmarks' => []
            ]
        );

        $meta = [$this->getDocumentService()->getMetadataKey() => $details];

        $uploader = $this->getUploader();
        $uploader->setFile(
            [
                'name' => $publication['docTemplate']['description'],
                'content' => $content,
                'meta'    => $meta,
            ]
        );

        //we need to increment the publication status here as the existing record will contain the old status
        switch ($publication['pubStatus']['id']) {
            case 'pub_s_new':
                $publication['pubStatus']['description'] = 'generated';
                break;
            case 'pub_s_generated':
                $publication['pubStatus']['description'] = 'printed';
                break;
        }

        $urlParams = $this->getFilePathVariablesFromPublication($publication);
        $documentPath = $uploader->buildPathNamespace($urlParams);

        return $uploader->upload($documentPath);
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

    /**
     * Returns a Jackrabbit client
     *
     * @return \Dvsa\Jackrabbit\Service\Client
     */
    protected function getContentStore()
    {
        return $this->getServiceLocator()->get('ContentStore');
    }

    /**
     * Gets the document service
     *
     * @return \Common\Service\Document\Document
     */
    protected function getDocumentService()
    {
        return $this->getServiceLocator()->get('Document');
    }

    protected function getDocumentDataService()
    {
        return $this->getServiceLocator()->get('DataServiceManager')->get('Generic\Service\Data\Document');
    }

    /**
     * Get uploader
     *
     * @return \Common\Service\File\FileUploaderFactory
     */
    protected function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader')->getUploader();
    }
}
