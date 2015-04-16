<?php

/**
 * Variation Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Variation Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class VariationTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    /**
     * Load data into the table
     */
    public function getTableData($variationId, $licenceId)
    {
        // Get TM's attached to licence
        /* @var $service \Common\Service\Entity\TransportManagerApplicationEntityService */
        $service = $this->getServiceLocator()->get('Entity\TransportManagerLicence');
        $licenceData = $service->getByLicenceWithHomeContactDetails($licenceId);

        $tableData = [];
        foreach ($licenceData['Results'] as $row) {
            $tableData[$row['transportManager']['id']] = [
                // Transport Manager Licence ID
                'id' => 'L'. $row['id'],
                'name' => $row['transportManager']['homeCd']['person'],
                'status' => null,
                'email' => $row['transportManager']['homeCd']['emailAddress'],
                'dob' => $row['transportManager']['homeCd']['person']['birthDate'],
                'transportManager' => $row['transportManager'],
                'action' => 'E',
            ];
        }

        // Get TM's attached to variation and merge them
        /* @var $service \Common\Service\Entity\TransportManagerLicenceEntityService */
        $service = $this->getServiceLocator()->get('Entity\TransportManagerApplication');
        $variationData = $service->getByApplicationWithHomeContactDetails($variationId);

        foreach ($variationData['Results'] as $row) {
            $tableData[$row['transportManager']['id'].'a'] = [
                // Transport Manager Application ID
                'id' => $row['id'],
                'name' => $row['transportManager']['homeCd']['person'],
                'status' => $row['tmApplicationStatus'],
                'email' => $row['transportManager']['homeCd']['emailAddress'],
                'dob' => $row['transportManager']['homeCd']['person']['birthDate'],
                'transportManager' => $row['transportManager'],
                'action' => $row['action'],
            ];
            switch ($row['action']) {
                case 'U':
                    // Mark original was as the current
                    $tableData[$row['transportManager']['id']]['action'] = 'C';
                    break;
                case 'D':
                    // Remove the original so that just the Delete version appears
                    unset($tableData[$row['transportManager']['id']]);
                    break;
            }
        }

        // sort them to make sure updated names are next to each other
        ksort($tableData);

        return $tableData;
    }

    /**
     * Delete Transport Managers from variation
     *
     * @param array $ids Transport Manager and Transport Manager Application ID's, Licence TM's are prefixed with "L"
     */
    public function delete(array $ids, $applicationId)
    {
        $transportManagerApplicationIds = [];
        foreach ($ids as $id) {
            // if has "L" prefix then its a TM Licence ID, else it is a TM Application ID
            if (strpos($id, 'L') === 0) {
                $transportManagerLicenceId = (int) trim($id, 'L');

                $service = $this->getServiceLocator()
                    ->get('BusinessServiceManager')
                    ->get('Lva\DeltaDeleteTransportManagerLicence');
                $service->process(
                    ['transportManagerLicenceId' => $transportManagerLicenceId, 'applicationId' => $applicationId]
                );
            } else {
                // add TMA is onto list to delete
                $transportManagerApplicationIds[] = $id;
            }
        }

        // if any TMA IDs, then delete them all
        if (count($transportManagerApplicationIds)> 0) {
            /* @var $service \Common\BusinessService\Service\TransportManagerApplication\Delete */
            $service = $this->getServiceLocator()
                ->get('BusinessServiceManager')
                ->get('Lva\DeleteTransportManagerApplication');
            $service->process(['ids' => $transportManagerApplicationIds]);
        }
    }
}
