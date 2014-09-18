<?php

/**
 * Authorisation Controller
 *
 * External - Application - Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace Common\Controller\Application\OperatingCentres;

use Common\Controller\Traits\OperatingCentre;

/**
 * Authorisation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class AuthorisationController extends OperatingCentresController
{
    use OperatingCentre\GenericApplicationAuthorisationSection,
        OperatingCentre\ExternalApplicationAuthorisationSection;

    /**
     * Northern Ireland Traffic Area Code
     */
    const NORTHERN_IRELAND_TRAFFIC_AREA_CODE = 'N';

    /**
     * Remove trailers for PSV
     *
     * @param Form $form
     */
    protected function alterActionForm($form)
    {
        if ($this->isPsv()) {
            $form->get('data')->remove('noOfTrailersPossessed');
            $form->remove('advertisements');

            $label = $form->get('data')->getLabel();
            $form->get('data')->setLabel($label .= '-psv');

            $label = $form->get('data')->get('sufficientParking')->getLabel();
            $form->get('data')->get('sufficientParking')->setLabel($label .= '-psv');

            $label = $form->get('data')->get('permission')->getLabel();
            $form->get('data')->get('permission')->setLabel($label .= '-psv');
        } else {

            $this->processFileUploads(
                array('advertisements' => array('file' => 'processAdvertisementFileUpload')),
                $form
            );

            $fileList = $form->get('advertisements')->get('file')->get('list');

            $bundle = array(
                'properties' => array(
                    'id',
                    'version',
                    'identifier',
                    'filename',
                    'size'
                )
            );

            $unlinkedFileData = $this->makeRestCall(
                'Document',
                'GET',
                array(
                    'application' => $this->getIdentifier(),
                    // @todo Add a better way to find the category id
                    'category' => 1,
                    'documentSubCategory' => 2,
                    'operatingCentre' => 'NULL'
                ),
                $bundle
            );

            $fileData = array();

            if ($this->getActionName() == 'edit') {
                $fileData = $this->actionLoad($this->getActionId())['operatingCentre']['adDocuments'];
            }

            $fileData = array_merge($fileData, $unlinkedFileData['Results']);

            $fileList->setFiles($fileData, $this->url());

            $this->processFileDeletions(array('advertisements' => array('file' => 'deleteFile')), $form);
        }

        // add traffic area validator
        $licenceData = $this->getLicenceData();

        $trafficAreaValidator = $this->getServiceLocator()->get('postcodeTrafficAreaValidator');
        $trafficAreaValidator->setNiFlag($licenceData['niFlag']);
        $trafficAreaValidator->setOperatingCentresCount($this->getOperatingCentresCount());
        $trafficAreaValidator->setTrafficArea($this->getTrafficArea());

        $postcodeValidatorChain = $this->getPostcodeValidatorsChain($form);
        $postcodeValidatorChain->attach($trafficAreaValidator);

        $form->getInputFilter()->get('address')->get('postcode')->setRequired(false);

        if ($licenceData['niFlag'] == 'N' && !$this->getTrafficArea()) {
            $form->get('form-actions')->remove('addAnother');
        }

        return $form;
    }

    /**
     * Save the operating centre
     *
     * @param array $data
     * @param string $service
     * @return null|Response
     */
    protected function actionSave($data, $service = null)
    {
        $saved = parent::actionSave($data['operatingCentre'], 'OperatingCentre');

        if ($this->getActionName() == 'add') {
            if (!isset($saved['id'])) {
                throw new \Exception('Unable to save operating centre');
            }

            $data['applicationOperatingCentre']['operatingCentre'] = $saved['id'];

            $operatingCentreId = $saved['id'];
        } else {
            $operatingCentreId = $data['operatingCentre']['id'];
        }

        if (isset($data['applicationOperatingCentre']['file']['list'])) {
            foreach ($data['applicationOperatingCentre']['file']['list'] as $file) {
                $this->makeRestCall(
                    'Document',
                    'PUT',
                    array('id' => $file['id'], 'version' => $file['version'], 'operatingCentre' => $operatingCentreId)
                );
            }
        }

        if ($this->isPsv()) {
            $data['applicationOperatingCentre']['adPlaced'] = 0;
        }

        $saved = parent::actionSave($data['applicationOperatingCentre'], $service);

        if ($this->getActionName() == 'add' && !isset($saved['id'])) {
            throw new \Exception('Unable to save application operating centre');
        }

        // set default Traffic Area if we don't have one
        if (!array_key_exists('trafficArea', $data) || !$data['trafficArea']['id']) {
            $licenceData = $this->getLicenceData();
            if ($licenceData['niFlag'] == 'Y') {
                $this->setTrafficArea(self::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
            }
            if ($licenceData['niFlag'] == 'N' && $data['operatingCentre']['addresses']['address']['postcode']) {
                $ocCount = $this->getOperatingCentresCount();

                // first Operating Centre was just added or we are editing the first one
                if ($ocCount == 1) {
                    $postcodeService = $this->getPostcodeService();
                    list($trafficAreaId, $trafficAreaName) =
                        $postcodeService->getTrafficAreaByPostcode(
                            $data['operatingCentre']['addresses']['address']['postcode']
                        );
                    if ($trafficAreaId) {
                        $this->setTrafficArea($trafficAreaId);
                    }
                }
            }
        }
    }

    /**
     * Save method
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        if (isset($data['trafficArea']) && $data['trafficArea']) {
            $this->setTrafficArea($data['trafficArea']);
        }
        parent::save($data, $service);
    }

    /**
     * Process the action load data
     *
     * @param array $oldData
     */
    protected function processActionLoad($oldData)
    {
        $data['data'] = $oldData;

        if ($this->getActionName() != 'add') {
            $data['operatingCentre'] = $data['data']['operatingCentre'];
            $data['address'] = $data['operatingCentre']['address'];
            $data['address']['countryCode'] = $data['address']['countryCode']['id'];

            $data['advertisements'] = array(
                'adPlaced' => $data['data']['adPlaced'],
                'adPlacedIn' => $data['data']['adPlacedIn'],
                'adPlacedDate' => $data['data']['adPlacedDate']
            );

            unset($data['data']['adPlaced']);
            unset($data['data']['adPlacedIn']);
            unset($data['data']['adPlacedDate']);
            unset($data['data']['operatingCentre']);
        }

        $data['data']['application'] = $this->getIdentifier();
        $trafficArea = $this->getTrafficArea();
        if (is_array($trafficArea) && array_key_exists('id', $trafficArea)) {
            $data['trafficArea']['id'] = $trafficArea['id'];
        }

        return $data;
    }

    /**
     * Process the loading of data
     *
     * @param array $oldData
     */
    protected function processLoad($oldData)
    {
        $results = $this->getFormTableData($this->getIdentifier(), '');

        $data['data'] = $oldData;

        $data['data']['noOfOperatingCentres'] = count($results);
        $data['data']['minVehicleAuth'] = 0;
        $data['data']['maxVehicleAuth'] = 0;
        $data['data']['minTrailerAuth'] = 0;
        $data['data']['maxTrailerAuth'] = 0;
        $data['data']['licenceType'] = $this->getLicenceType();
        foreach ($results as $row) {

            $data['data']['minVehicleAuth'] = max(
                array($data['data']['minVehicleAuth'], $row['noOfVehiclesPossessed'])
            );
            $data['data']['minTrailerAuth'] = max(
                array($data['data']['minTrailerAuth'], $row['noOfTrailersPossessed'])
            );
            $data['data']['maxVehicleAuth'] += (int) $row['noOfVehiclesPossessed'];
            $data['data']['maxTrailerAuth'] += (int) $row['noOfTrailersPossessed'];
        }

        if (is_array($oldData) && array_key_exists('licence', $oldData) &&
            array_key_exists('trafficArea', $oldData['licence']) &&
            is_array($oldData['licence']['trafficArea']) &&
            array_key_exists('id', $oldData['licence']['trafficArea'])) {
            $data['dataTrafficArea']['hiddenId'] = $oldData['licence']['trafficArea']['id'];
        }
        return $data;
    }

    /**
     * Handle the file upload
     *
     * @param array $file
     */
    protected function processAdvertisementFileUpload($file)
    {
        $this->uploadFile(
            $file,
            array(
                'description' => 'Advertisement',
                // @todo Add a better way to find the category id
                'category' => 1,
                'documentSubCategory' => 2
            )
        );
    }

    /**
     * Check for actions
     *
     * @param string $route
     * @param array $params
     * @param string $itemIdParam
     *
     * @return boolean
     */
    public function checkForCrudAction($route = null, $params = array(), $itemIdParam = 'id')
    {
        $table = $this->params()->fromPost('table');
        $action = isset($table['action'])
            ? strtolower($table['action'])
            : strtolower($this->params()->fromPost('action'));

        if (empty($action)) {
            return false;
        }

        $params = array_merge($params, array('action' => $action));

        if ($action !== 'add') {
            $id = $this->params()->fromPost('id');

            if (empty($id)) {

                return false;
            }

            $params[$itemIdParam] = $id;
        }
        if (!$this->getTrafficArea()) {
            $dataTrafficArea = $this->params()->fromPost('dataTrafficArea');
            $trafficArea = is_array($dataTrafficArea) && isset($dataTrafficArea['trafficArea']) ?
                $dataTrafficArea['trafficArea'] : '';
            if ($action == 'add' && !$trafficArea && $this->getOperatingCentresCount()) {
                $this->addWarningMessage('Please select a traffic area');
                return $this->redirectToRoute(null, array(), array(), true);
            } elseif ($action == 'add' && $trafficArea) {
                $this->setTrafficArea($trafficArea);
            }
        }

        return $this->redirect()->toRoute($route, $params, [], true);
    }
}
