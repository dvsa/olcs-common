<?php

namespace Common\Controller\Lva\Adapters;

use Dvsa\Olcs\Transfer\Command;

/**
 * Variation Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class VariationTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    /**
     * Load data into the table
     */
    public function getTableData($variationId, $licenceId)
    {
        $query = $this->transferAnnotationBuilder->createQuery(
            \Dvsa\Olcs\Transfer\Query\Application\TransportManagers::create(['id' => $variationId])
        );

        /* @var $response \Common\Service\Cqrs\Response */
        $data = $this->querySrv->send($query)->getResult();

        return $this->mapResultForTable($data['transportManagers'], $data['licence']['tmLicences']);
    }

    /**
     * Delete Transport Managers from variation
     *
     * @param array $ids Transport Manager and Transport Manager Application ID's, Licence TM's are prefixed with "L"
     */
    public function delete(array $ids, $applicationId)
    {
        $tmlIds = [];
        $tmaIds = [];
        foreach ($ids as $id) {
            // if has "L" prefix then its a TM Licence ID, else it is a TM Application ID
            if (strpos($id, 'L') === 0) {
                $tmlIds[] = (int) trim($id, 'L');
            } else {
                $tmaIds[] = (int) $id;
            }
        }

        if (count($tmaIds) !== 0) {
            $command = $this->transferAnnotationBuilder->createCommand(
                Command\TransportManagerApplication\Delete::create(['ids' => $tmaIds])
            );
            $this->commandSrv->send($command);
        }

        if (count($tmlIds) !== 0) {
            $command = $this->transferAnnotationBuilder->createCommand(
                Command\Variation\TransportManagerDeleteDelta::create(
                    ['id' => $applicationId, 'transportManagerLicenceIds' => $tmlIds]
                )
            );
            $this->commandSrv->send($command);
        }
    }
}
