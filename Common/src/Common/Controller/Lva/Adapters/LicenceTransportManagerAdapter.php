<?php

namespace Common\Controller\Lva\Adapters;

use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteUpdateOptOutTmLetter;

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

    /**
     * get table data
     *
     * @param null $applicationId not used here but needed to conform to interface
     * @param int  $licenceId     licence id
     *
     * @return array|null
     */
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

    /**
     * delete a transport manager from a licence
     *
     * @param array $ids           ids to be deleted
     * @param null  $applicationId not used here but needed to conform to interface
     * @param mixed $optOut        data from InternalGenericDeleteConfirmation
     * @param bool  $isLastTm      checks if last TM is being deleted from licence
     *
     * @return bool
     */
    public function delete(array $ids, $applicationId, $optOut, $isLastTm = null)
    {
        if($isLastTm) {
            $command = $this->transferAnnotationBuilder->createCommand(DeleteUpdateOptOutTmLetter::create(
                [
                    'ids' => $ids,
                    'YesNo' => $optOut["YesNoRadio"]["yesNo"],
                ]
            ));
        } else {
            $command = $this->transferAnnotationBuilder->createCommand(Delete::create(['ids' => $ids]));
        }
        return $this->commandSrv->send($command)->isOk();
    }




}
