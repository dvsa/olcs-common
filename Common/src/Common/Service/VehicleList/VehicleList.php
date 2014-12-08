<?php

/**
 * Service to get create vehicle list and save it to JackRabbit
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\VehicleList;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Service to get create vehicle list and save it to JackRabbit
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehicleList implements ServiceLocatorAwareInterface
{
    use \Common\Util\RestCallTrait,
        ServiceLocatorAwareTrait;

    /**
     * @var array
     */
    protected $queryData = [];

    /**
     * @var array
     */
    protected $bookmarkData = [];

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $description;

    /**
     * Generate vehicle list
     * $serveFile param will work if we need to generate one file only
     * 
     * @param bool $serveFile
     * @return string|bool
     */
    public function generateVehicleList($serveFile = false)
    {
        if (empty($this->getQueryData())) {
            return false;
        }

        $documentService = $this->getServiceLocator()->get('Document');

        $categoryService = $this->getServiceLocator()->get('category');

        $category    = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Vehicle List', 'Document');

        /* ContentStore service doesn't clear
         * previous request method so we need to be sure
         * that we use GET not POST
         */
        $this->setContentStoreMethod('GET');

        $file = $this->getServiceLocator()
            ->get('ContentStore')
            ->read('/templates/' . $this->getTemplate() . '.rtf');

        if (!$file) {
            throw new Exception('Error getting template file');
        }

        $bookmarkData = $this->getBookmarkData();

        foreach ($this->getQueryData() as $key => $queryData) {
            $query = $documentService->getBookmarkQueries($file, $queryData);

            if (!is_array($query) || empty($query)) {
                throw new Exception('Error getting bookmark queries');
            }

            $result = $this->makeRestCall('BookmarkSearch', 'GET', [], $query);
            if (!is_array($result) || empty($result)) {
                throw new Exception('Error getting bookmarks');
            }

            if (isset($bookmarkData[$key])) {
                $result = array_merge(
                    $result,
                    $bookmarkData[$key]
                );
            }

            $content = $documentService->populateBookmarks($file, $result);

            $uploader = $this->getServiceLocator()
                ->get('FileUploader')
                ->getUploader();

            $uploader->setFile(['content' => $content]);

            $uploadedFile = $uploader->upload();

            $filename = $this->getFilename() . '.rtf';
            $fileName = $this->getServiceLocator()
                ->get('Helper\Date')
                ->getDate('YmdHis') . '_' . $filename;

            $data = [
                'licence'             => $queryData['licence'],
                'identifier'          => $uploadedFile->getIdentifier(),
                'description'         => $this->getDescription(),
                'filename'            => $fileName,
                'fileExtension'       => 'doc_rtf',
                'category'            => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'isDigital'           => true,
                'isReadOnly'          => true,
                'issuedDate'          => $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
                'size'                => $uploadedFile->getSize()
            ];

            $this->makeRestCall(
                'Document',
                'POST',
                $data
            );
        }

        if ($serveFile && count($this->getQueryData()) === 1) {
            /**
             * rather than have to go off and fetch the file again, just
             * update the content of the one we got back earlier from JR
             * and serve it directly
             */
            $file->setContent($content);

            return $uploader->serveFile($file, $fileName);
        }

        return true;
    }

    /**
     * Set content store method
     *
     * @param string $method
     */
    protected function setContentStoreMethod($method = 'GET')
    {
        $this->getServiceLocator()
            ->get('ContentStore')
            ->getHttpClient()
            ->getRequest()
            ->setMethod($method);
    }

    /**
     * Set query data
     *
     * @param array $queryData
     */
    public function setQueryData($queryData = [])
    {
        $this->queryData = $queryData;
        return $this;
    }

    /**
     * Get query data
     *
     * @return array
     */
    public function getQueryData()
    {
        return $this->queryData;
    }

    /**
     * Set template
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set bookmark data
     *
     * @param array $bookmarkData
     */
    public function setBookmarkData($bookmarkData = [])
    {
        $this->bookmarkData = $bookmarkData;
        return $this;
    }

    /**
     * Get bookmark data
     *
     * @return array
     */
    public function getBookmarkData()
    {
        return $this->bookmarkData;
    }

    protected function getFilename()
    {
        return str_replace(" ", "_", $this->getDescription());
    }
}
