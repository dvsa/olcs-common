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
        return [];
    }

    public function delete(array $ids, $applicationId)
    {
        // No-op
    }
}
