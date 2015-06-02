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
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\Application\TransportManagers::create(['id' => $variationId]));

        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('QueryService')->send($query);
        $data = $response->getResult();

        return $this->mapResultForTable($data['transportManagers'], $data['licence']['tmLicences']);
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
