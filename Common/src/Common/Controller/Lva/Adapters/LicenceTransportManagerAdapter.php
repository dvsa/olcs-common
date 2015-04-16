<?php

/**
 * Licence Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Licence Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    protected $lva = 'licence';
    protected $entityService = 'Entity\ApplicationOperatingCentre';

    public function getTableData($applicationId, $licenceId)
    {
        // Get TM's attached to licence
        /* @var $service \Common\Service\Entity\TransportManagerLicenceEntityService */
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
            ];
        }

        return $tableData;
    }

    public function delete(array $ids, $applicationId)
    {
        // No-op
    }
}
