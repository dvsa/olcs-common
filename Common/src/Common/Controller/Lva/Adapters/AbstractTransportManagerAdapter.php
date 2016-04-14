<?php

namespace Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Interfaces\TransportManagerAdapterInterface;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;

/**
 * Abstract Transport Manager Adapter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
abstract class AbstractTransportManagerAdapter extends AbstractControllerAwareAdapter implements
    TransportManagerAdapterInterface
{
    const SORT_LAST_FIRST_NAME = 1;
    const SORT_LAST_FIRST_NAME_NEW_AT_END = 2;

    /** @var TransferAnnotationBuilder */
    protected $transferAnnotationBuilder;
    /** @var CachingQueryService */
    protected $querySrv;
    /** @var CommandService */
    protected $commandSrv;

    protected $tableSortMethod = self::SORT_LAST_FIRST_NAME;

    public function __construct(
        TransferAnnotationBuilder $transferAnnotationBuilder,
        CachingQueryService $querySrv,
        CommandService $commandSrv
    ) {
        $this->transferAnnotationBuilder = $transferAnnotationBuilder;
        $this->querySrv = $querySrv;
        $this->commandSrv = $commandSrv;
    }

    /**
     * Get the table
     *
     * @return \Common\Service\Table\TableBuilder
     */
    public function getTable($template = 'lva-transport-manangers')
    {
        return $this->getServiceLocator()->get('Table')->prepareTable($template);
    }

    /**
     * Is this licence required to have at least one Transport Manager
     *
     * @return boolean
     */
    public function mustHaveAtLeastOneTm()
    {
        return false;
    }

    /**
     * Add any messages to the page
     */
    public function addMessages($licenceId)
    {
    }

    /**
     * Map array data from the Backend into arrays for CRUD tables
     *
     * @param array $applicationTms array of Transport Manager Applications
     * @param array $licenceTms     array of Transport Manager Licences
     *
     * @return array
     */
    protected function mapResultForTable(array $applicationTms, array $licenceTms = [])
    {
        $mappedData = [];

        // add each TM from the licence
        foreach ($licenceTms as $tml) {
            $mng = $tml['transportManager'];

            $homeCd = $mng['homeCd'];

            $mappedData[$mng['id']] = [
                // Transport Manager Licence ID
                'id' => 'L' . $tml['id'],
                'name' => $homeCd['person'],
                'status' => null,
                'email' => $homeCd['emailAddress'],
                'dob' => $homeCd['person']['birthDate'],
                'transportManager' => $mng,
                'action' => 'E',
            ];
        }

        // add each TM from the application/variation
        foreach ($applicationTms as $tma) {
            $mng = $tma['transportManager'];

            $id = $mng['id'];
            $homeCd = $mng['homeCd'];

            $mappedData[$id . 'a'] = [
                'id' => $tma['id'],
                'name' => $homeCd['person'],
                'status' => $tma['tmApplicationStatus'],
                'email' => $homeCd['emailAddress'],
                'dob' => $homeCd['person']['birthDate'],
                'transportManager' => $mng,
                'action' => $tma['action'],
            ];

            // update the licence TM's if they have been updated
            switch ($tma['action']) {
                case 'U':
                    // Mark original as the current
                    $mappedData[$id]['action'] = 'C';
                    break;
                case 'D':
                    // Remove the original so that just the Delete version appears
                    unset($mappedData[$id]);
                    break;
            }
        }

        return $this->sortResultForTable($mappedData, $this->tableSortMethod);
    }

    protected function sortResultForTable(array $data, $method = null)
    {
        if ($method === self::SORT_LAST_FIRST_NAME) {
            usort($data, [$this, 'sortCmpByName']);

            return $data;
        }

        if ($method === self::SORT_LAST_FIRST_NAME_NEW_AT_END) {
            usort($data, [$this, 'sortCmpByNameAndNewAtEnd']);

            return $data;
        }

        return $data;
    }

    /**
     * Comparition function for sorting a table by Last and First name
     *
     * @return int
     */
    private static function sortCmpByName($a, $b)
    {
        $keyA = strtolower($a['name']['familyName'] . $a['name']['forename']);
        $keyB = strtolower($b['name']['familyName'] . $b['name']['forename']);

        return strnatcmp($keyA, $keyB);
    }

    /**
     * Comparition function for sorting a table by Last and First name,
     * and all new items to the end
     *
     * @return int
     */
    private static function sortCmpByNameAndNewAtEnd($a, $b)
    {
        $isNewA = (int)($a['action'] === 'A');
        $isNewB = (int)($b['action'] === 'A');

        if ($isNewA != $isNewB) {
            return ($isNewA < $isNewB ? -1 : 1);
        }

        return static::sortCmpByName($a, $b);
    }
}
