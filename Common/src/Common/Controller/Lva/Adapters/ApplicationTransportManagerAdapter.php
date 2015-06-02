<?php

/**
 * Application Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Entity\LicenceEntityService;

/**
 * Application Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ApplicationTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    /**
     * Load data into the table
     */
    public function getTableData($applicationId, $licenceId)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\Application\TransportManagers::create(['id' => $applicationId]));

        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $data = $response->getResult();

        return $this->mapResultForTable($data['transportManagers']);
    }

    /**
     * Must this licence type have at least one Transport Manager
     *
     * @param int $applicationId Application ID
     *
     * @return bool
     */
    public function mustHaveAtLeastOneTm($applicationId)
    {
        /* @var $service \Common\Service\Entity\ApplicationEntityService */
        $service = $this->getServiceLocator()->get('Entity\Application');
        $application = $service->getLicenceType($applicationId);

        $mustHaveTypes = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        return in_array($application['licenceType']['id'], $mustHaveTypes);
    }

    /**
     * Delete Transport Managers
     *
     * @param array $ids Transport Manager Application IDs
     */
    public function delete(array $ids, $applicationId)
    {
        /* @var $service \Common\BusinessService\Service\TransportManagerApplication\Delete */
        $service = $this->getServiceLocator()
            ->get('BusinessServiceManager')
            ->get('Lva\DeleteTransportManagerApplication');
        $service->process(['ids' => $ids]);
    }
}
