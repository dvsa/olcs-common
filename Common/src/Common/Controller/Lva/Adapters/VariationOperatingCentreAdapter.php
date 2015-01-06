<?php

/**
 * Variation Operating Centre Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Operating Centre Adapter
 *
 * @NOTE This could potentially extends the ApplicationOperatingCentreAdapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreAdapter extends AbstractOperatingCentreAdapter
{
    public function attachScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-variation-operating-centre');
    }

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    public function getTableData()
    {
        if (empty($this->tableData)) {

            $licenceData = $this->getIndexedTableData('Licence', $this->getLicenceId());
            $applicationData = $this->getIndexedTableData('Application', $this->getApplicationId());

            $data = $this->updateAndFilterTableData($licenceData, $applicationData);

            $this->tableData = $this->formatTableData($data);
        }

        return $this->tableData;
    }

    protected function updateAndFilterTableData($licenceData, $applicationData)
    {
        $data = array();

        foreach ($licenceData as $ocId => $row) {

            if (!isset($applicationData[$ocId])) {
                // If we have no application oc record

                // E for existing (No updates)
                $row['action'] = 'E';
                $data[] = $row;
            } elseif ($applicationData[$ocId]['action'] === 'U') {
                // If we have updated the operating centre

                $row['action'] = 'C';
                $data[] = $row;
            }
        }

        $data = array_merge($data, $applicationData);

        return $data;
    }

    protected function getIndexedTableData($type, $id)
    {
        $data = $this->getServiceLocator()->get('Entity\\' . $type . 'OperatingCentre')
            ->getAddressSummaryData($id)['Results'];

        $indexedData = [];

        foreach ($data as $value) {
            $value['id'] = substr($type, 0, 1) . $value['id'];
            $indexedData[$value['operatingCentre']['id']] = $value;
        }

        return $indexedData;
    }

    protected function getTableConfigName()
    {
        return 'lva-variation-operating-centres';
    }
}
