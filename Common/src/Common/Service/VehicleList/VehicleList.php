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
    protected $licenceIds = [];

    /**
     * @var int
     */
    protected $loggedInUser = null;

    /**
     * Generate vehicle list
     * $serveFile param will work if we need to generate one file only
     * 
     * @param bools $serveFile
     * @return string|bool
     */
    public function generateVehicleList($serveFile = false)
    {
        $licenceIds = $this->getLicenceIds();

        if (!count($licenceIds)) {
            return false;
        }

        $documentService = $this->getServiceLocator()->get('Document');

        $categoryService = $this->getServiceLocator()->get('category');

        $category    = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Vehicle List', 'Document');

        $retv = true;

        /* ContentStore service doesn't clear
         * previous request method so we need to be sure
         * that we use GET not POST
         */
        $this->setContentStoreMethod('GET');

        $file = $this->getServiceLocator()
            ->get('ContentStore')
            ->read('/templates/GVVehiclesList.rtf');

        if (!$file) {
            throw new \Exception('Error getting template file');
        }

        foreach ($licenceIds as $licenceId) {

            $queryData = [
                'licence' => $licenceId,
                'user' => $this->getLoggedInUser()
            ];
            $query = $documentService->getBookmarkQueries($file, $queryData);
            if (!is_array($query) || !count($query)) {
                throw new \Exception('Error getting bookmark queries');
            }

            $result = $this->makeRestCall('BookmarkSearch', 'GET', [], $query);
            if (!is_array($result) || !count($result)) {
                throw new \Exception('Error getting bookmarks');
            }

            $content = $documentService->populateBookmarks($file, $result);

            $uploader = $this->getServiceLocator()
                ->get('FileUploader')
                ->getUploader();

            $uploader->setFile(['content' => $content]);

            $uploadedFile = $uploader->upload();

            $fileName = date('YmdHi') . '_' . 'Goods_Vehicle_List.rtf';

            $data = [
                'licence'             => $licenceId,
                'identifier'          => $uploadedFile->getIdentifier(),
                'description'         => 'Goods Vehicle List',
                'filename'            => $fileName,
                'fileExtension'       => 'doc_rtf',
                'category'            => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'isDigital'           => true,
                'isReadOnly'          => true,
                'issuedDate'          => date('Y-m-d H:i:s'),
                'size'                => $uploadedFile->getSize()
            ];

            $this->makeRestCall(
                'Document',
                'POST',
                $data
            );
        }

        if ($serveFile && count($licenceIds) == 1) {
            /**
             * rather than have to go off and fetch the file again, just
             * update the content of the one we got back earlier from JR
             * and serve it directly
             */
            $file->setContent($content);

            $retv = $uploader->serveFile($file, $fileName);
        }

        return $retv;

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
     * Set licenceIds
     *
     * @param array $licenceIds
     */
    public function setLicenceIds($licenceIds = [])
    {
        $this->licenceIds = $licenceIds;
    }

    /**
     * Get licenceIds
     *
     * @return $licenceIds
     */
    public function getLicenceIds()
    {
        return $this->licenceIds;
    }

    /**
     * Set logged in user
     *
     * @param in $loggedInUser
     */
    public function setLoggedInUser($loggedInUser = [])
    {
        $this->loggedInUser = $loggedInUser;
    }

    /**
     * Get logged in user
     *
     * @return $logged in user
     */
    public function getLoggedInUser()
    {
        return $this->loggedInUser;
    }
}
