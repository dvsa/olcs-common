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
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\Licence\TransportManagers::create(['id' => $licenceId]));

        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $data = $response->getResult();

        return $this->mapResultForTable([], $data['tmLicences']);
    }

    public function delete(array $ids, $applicationId)
    {
        // No-op
    }
}
