<?php

namespace Common\Controller\Lva\Adapters;

/**
 * Licence Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class LicenceTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    protected $lva = 'licence';
    protected $entityService = 'Entity\ApplicationOperatingCentre';

    public function getTableData($applicationId, $licenceId)
    {
        $query = $this->transferAnnotationBuilder->createQuery(
            \Dvsa\Olcs\Transfer\Query\Licence\TransportManagers::create(['id' => $licenceId])
        );

        $response = $this->querySrv->send($query);
        return $response->isForbidden()
            ? null
            : $this->mapResultForTable([], $response->getResult()['tmLicences']);
    }

    public function delete(array $ids, $applicationId)
    {
        // No-op
    }
}
