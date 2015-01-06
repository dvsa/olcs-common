<?php

/**
 * Abstract Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractControllerAwareAdapter;
use Common\Controller\Lva\Interfaces\OperatingCentreAdapterInterface;

/**
 * Abstract Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractOperatingCentreAdapter extends AbstractControllerAwareAdapter implements
    OperatingCentreAdapterInterface
{
    protected $tableData;

    protected $entityService;

    public function attachScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');
    }

    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties()
    {
        return array(
            'application' => $this->getApplicationAdapter()->getIdentifier(),
            'licence' => $this->getLicenceAdapter()->getIdentifier()
        );
    }

    /**
     * Get operating centre data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresFormData($id)
    {
        return $this->formatDataForForm(
            $this->getServiceLocator()->get($this->entityService)->getOperatingCentresData($id),
            $this->getTableData(),
            $this->getTypeOfLicenceData()
        );
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    public function getTableData()
    {
        if (empty($this->tableData)) {

            $id = $this->getApplicationAdapter()->getIdentifier();

            $data = $this->getServiceLocator()->get($this->entityService)
                ->getAddressSummaryData($id);

            $this->tableData = $this->formatTableData($data['Results']);
        }

        return $this->tableData;
    }

    public function getMainForm()
    {
        $form = $this->createMainForm();

        $table = $this->createMainTable();

        $form->get('table')->get('table')->setTable($table);

        $this->alterForm($form);

        return $form;
    }

    protected function alterForm()
    {
        // @todo implement this
    }

    protected function getTableConfigName()
    {
        return 'lva-operating-centres';
    }

    protected function getTypeOfLicenceData()
    {
        // @todo implement this
    }

    protected function createMainTable()
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable($this->getTableConfigName(), $this->getTableData());
    }

    protected function createMainForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\OperatingCentres');
    }

    protected function formatDataForForm($data, $tableData, $licenceData)
    {
        $data['data'] = $oldData = $data;

        $data['data']['noOfOperatingCentres'] = count($tableData);
        $data['data']['minVehicleAuth'] = 0;
        $data['data']['maxVehicleAuth'] = 0;
        $data['data']['minTrailerAuth'] = 0;
        $data['data']['maxTrailerAuth'] = 0;
        $data['data']['licenceType'] = $licenceData['licenceType'];

        foreach ($tableData as $row) {

            $data['data']['minVehicleAuth'] = max(
                array($data['data']['minVehicleAuth'], $row['noOfVehiclesRequired'])
            );

            $data['data']['minTrailerAuth'] = max(
                array($data['data']['minTrailerAuth'], $row['noOfTrailersRequired'])
            );

            $data['data']['maxVehicleAuth'] += (int)$row['noOfVehiclesRequired'];
            $data['data']['maxTrailerAuth'] += (int)$row['noOfTrailersRequired'];
        }

        if (isset($oldData['licence']['trafficArea']['id'])) {
            $data['dataTrafficArea']['hiddenId'] = $oldData['licence']['trafficArea']['id'];
        }

        return $data;
    }

    protected function formatTableData($results)
    {
        $newData = array();

        foreach ($results as $row) {

            $newRow = $row;

            if (isset($row['operatingCentre']['address'])) {

                unset($row['operatingCentre']['address']['id']);
                unset($row['operatingCentre']['address']['version']);

                $newRow = array_merge($newRow, $row['operatingCentre']['address']);
            }

            unset($newRow['operatingCentre']);

            $newData[] = $newRow;
        }

        return $newData;
    }
}
