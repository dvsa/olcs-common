<?php

/**
 * Generic Application Authorisation Section
 *
 * Internal/External - Application Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * Generic Application Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait GenericApplicationAuthorisationSection
{
    use GenericAuthorisationSection;

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
    protected $sharedActionService = 'ApplicationOperatingCentre';

    /**
     * Holds the section service
     *
     * @var string
     */
    protected $sharedService = 'Application';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $sharedDataBundle = array(
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
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->renderSection();
    }

    /**
     * Add operating centre
     */
    public function addAction()
    {
        return $this->renderSection();
    }

    /**
     * Retrieve the relevant table data as we want to render it on the review summary page
     * Note that as with most controllers this is the same data we want to render on the
     * normal form page, hence why getFormTableData (declared later) simply wraps this
     */
    protected static function getSummaryTableData($id, $context, $tableName)
    {
        $data = $context->makeRestCall(
            'ApplicationOperatingCentre',
            'GET',
            array('application' => $id),
            static::$tableDataBundle
        );

        return static::formatSummaryTableData($data);
    }

    /**
     * Get operating centres count
     *
     * @return int
     */
    protected function getOperatingCentresCount()
    {
        $operatingCentres = $this->makeRestCall(
            $this->sharedActionService,
            'GET',
            array('application' => $this->getIdentifier()),
            $this->ocCountBundle
        );

        return $operatingCentres['Count'];
    }

    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods($form)
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
     * Do action save
     *
     * @param array $data
     * @param string $service
     * @return mixed
     */
    protected function doActionSave($data, $service)
    {
        return parent::actionSave($data, $service);
    }

    /**
     * Alter the form
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function alterForm($form)
    {
        return $this->doAlterForm($form);
    }

    /**
     * Extend the generic process load method
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        return $this->doProcessLoad($data);
    }

    /**
     * Process save crud
     *
     * @param array $data
     */
    protected function processSaveCrud($data)
    {
        $action = strtolower($data['table']['action']);

        if (!$this->getTrafficArea() && $action == 'add') {
            $trafficArea = isset($data['dataTrafficArea']['trafficArea'])
                ? $data['dataTrafficArea']['trafficArea']
                : '';

            if (empty($trafficArea) && $this->getOperatingCentresCount()) {
                $this->addWarningMessage('select-traffic-area-error');
                $this->setCaughtResponse($this->redirect()->toRoute(null, array(), array(), true));
                return;
            }

            if (!empty($trafficArea)) {
                $this->setTrafficArea($trafficArea);
            }
        }

        return parent::processSaveCrud($data);
    }
}
