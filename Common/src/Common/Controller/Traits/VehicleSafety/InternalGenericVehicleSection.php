<?php

/**
 * Internal Generic Vehicle Section
 *
 * Internal - Application/Licence - Vehicle/VehiclePsv Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\VehicleSafety;

/**
 * Internal Generic Vehicle Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait InternalGenericVehicleSection
{
    protected $sectionLocation = 'Internal';

    protected $sharedBespokeSubActions = array('reprint', 'vehicles');

    /**
     * Holds the table data bundle
     *
     * @var array
     */
    protected $historyTableDataBundle = array(
        'properties' => array(
            'id',
            'vrm',
            'licenceNo',
            'specifiedDate',
            'removalDate',
            'discNo'
        )
    );


    /**
     * Holds the bundle to retrieve VRM
     *
     * @var array
     */
    protected $vehicleBundle = array(
        'properties' => array(
            'id'
        ),
        'children' => array(
            'vehicle' => array(
                'properties' => array(
                    'vrm'
                )
            )
        )
    );

    /**
     * Alter the action form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterActionForm($form)
    {
        return $this->doAlterActionForm($form);
    }

    /**
     * Get the form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getActionTableData($id)
    {
        $vehicleId = $this->getActionId();

        if ( is_null($vehicleId) ) {
            return array();
        }

        $vrmData = $this->makeRestCall(
            'LicenceVehicle',
            'GET',
            array('id' => $vehicleId),
            $this->vehicleBundle
        );

        $data = $this->makeRestCall(
            'VehicleHistoryView',
            'GET',
            array(
                'vrm' => $vrmData['vehicle']['vrm'],
                'sort' => 'specifiedDate',
                'order' => 'DESC'
            ),
            $this->historyTableDataBundle
        );

        return $data;
    }


    /**
     * Shared logic between internal vehicle sections
     *
     * @param array $data
     * @param string $action
     * @return mixed
     */
    protected function internalActionSave($data, $action)
    {
        if ($action == 'add') {
            $data['licence-vehicle']['specifiedDate'] = date('Y-m-d');
        }

        return $this->doActionSave($data, $action);
    }

    /**
     * Print vehicles action
     */
    public function printVehiclesAction()
    {
        $documentService = $this->getServiceLocator()->get('Document');

        $file = $this->getServiceLocator()
            ->get('ContentStore')
            ->read('/templates/GVVehiclesList.rtf');

        $queryData = [
            'licence' => $this->getLicenceId(),
            'user' => $this->getLoggedInUser()
        ];
        $query = $documentService->getBookmarkQueries($file, $queryData);

        $result = $this->makeRestCall('BookmarkSearch', 'GET', [], $query);

        $content = $documentService->populateBookmarks($file, $result);

        $uploader = $this->getServiceLocator()
            ->get('FileUploader')
            ->getUploader();

        $uploader->setFile(['content' => $content]);

        $categoryService = $this->getServiceLocator()->get('category');

        $category    = $categoryService->getCategoryByDescription('Licensing');
        $subCategory = $categoryService->getCategoryByDescription('Vehicle List', 'Document');

        $uploadedFile = $uploader->upload();

        $fileName = date('YmdHi') . '_' . 'Goods_Vehicle_List.rtf';

        // @NOTE: not pretty, but this will be absorbed into all the LVA rework anyway in which
        // this is solved
        $lvaType = strtolower($this->sectionType);

        $data = [
            $lvaType              => $this->getIdentifier(),
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

        /**
         * rather than have to go off and fetch the file again, just
         * update the content of the one we got back earlier from JR
         * and serve it directly
         */
        $file->setContent($content);

        return $uploader->serveFile($file, $fileName);
    }
}
