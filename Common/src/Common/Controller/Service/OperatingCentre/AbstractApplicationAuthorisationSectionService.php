<?php

/**
 * Abstract Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;

/**
 * Abstract Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractApplicationAuthorisationSectionService extends AbstractAuthorisationSectionService
{
    /**
     * Category Service
     *
     * @var \Common\Service\Data\CategoryData
     */
    protected $categoryService;

    /**
     * Holds the sub action service
     *
     * @var string
     */
    protected $actionService = 'ApplicationOperatingCentre';

    /**
     * Action Identifier
     *
     * @var string
     */
    protected $actionIdentifier = 'application';

    /**
     * Holds the section service
     *
     * @var string
     */
    protected $service = 'Application';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'totAuthSmallVehicles',
            'totAuthMediumVehicles',
            'totAuthLargeVehicles',
            'totCommunityLicences',
            'totAuthVehicles',
            'totAuthTrailers',
        ),
        'children' => array(
            'licence' => array(
                'properties' => array(
                    'id'
                ),
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'id',
                            'name'
                        )
                    )
                )
            ),
            'operatingCentre' => array(
                'properties' => array(
                    'id',
                    'version'
                ),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array('id')
                            )
                        )
                    ),
                    'adDocuments' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'filename',
                            'identifier',
                            'size'
                        )
                    )
                )
            )
        )
    );

    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        $this->processFileUploads(array('advertisements' => array('file' => 'processAdvertisementFileUpload')), $form);

        $fileData = $this->getUnlinkedFileData()['Results'];

        if ($this->getActionName() == 'edit') {
            $fileData = array_merge(
                $fileData,
                $this->actionLoad($this->getActionId())['operatingCentre']['adDocuments']
            );
        }

        $form->get('advertisements')->get('file')->get('list')->setFiles($fileData, $this->url());

        $this->processFileDeletions(array('advertisements' => array('file' => 'deleteFile')), $form);
    }

    /**
     * Get unlinked file data
     *
     * @return array
     */
    protected function getUnlinkedFileData()
    {
        $category = $this->getCategoryService()->getCategoryByDescription('Licensing');
        $subCategory = $this->getCategoryService()->getCategoryByDescription('Advertisement', 'Document');

        return $this->makeRestCall(
            'Document',
            'GET',
            array(
                'application' => $this->getIdentifier(),
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id'],
                'operatingCentre' => 'NULL'
            ),
            $this->getDocumentBundle
        );
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    protected function processAdvertisementFileUpload($file)
    {
        $category = $this->getCategoryService()->getCategoryByDescription('Licensing');
        $subCategory = $this->getCategoryService()->getCategoryByDescription('Advertisement', 'Document');

        $this->uploadFile(
            $file,
            array(
                'description' => 'Advertisement',
                'category' => $category['id'],
                'documentSubCategory' => $subCategory['id']
            )
        );
    }

    /**
     * Get category service
     *
     * @return \Common\Service\Data\CategoryData
     */
    protected function getCategoryService()
    {
        if ($this->categoryService == null) {
            $this->categoryService = $this->getServiceLocator()->get('category');
        }

        return $this->categoryService;
    }

    /**
     * Set Traffic Area After Crud Action
     *
     * @param array $data
     */
    public function setTrafficAreaAfterCrudAction($data)
    {
        $action = strtolower($data['table']['action']);

        if (!$this->getTrafficArea() && $action == 'add') {
            $trafficArea = isset($data['dataTrafficArea']['trafficArea'])
                ? $data['dataTrafficArea']['trafficArea']
                : '';

            if (empty($trafficArea) && $this->getOperatingCentresCount()) {
                return false;
            }

            if (!empty($trafficArea)) {
                $this->getSectionService('TrafficArea')->setTrafficArea($trafficArea);
            }
        }

        return true;
    }

    /**
     * Get licence section service
     *
     * @NOTE Going via the application service, sets the licenceId into the service, rather than the appId
     *
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    protected function getLicenceSectionService()
    {
        return $this->getSectionService('Application')->getLicenceSectionService();
    }
}
