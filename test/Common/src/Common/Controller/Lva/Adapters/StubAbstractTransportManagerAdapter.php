<?php

namespace CommonTest\Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractTransportManagerAdapter;

/**
 * Stub class for abstract transport manager adapter testing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class StubAbstractTransportManagerAdapter extends AbstractTransportManagerAdapter
{
    protected $tableSortMethod;

    public function mapResultForTable(array $applicationTms, array $licenceTms = [])
    {
        return parent::mapResultForTable($applicationTms, $licenceTms);
    }

    public function sortResultForTable(array $data, $method = null)
    {
        return parent::sortResultForTable($data, $method);
    }

    public function getTableData($applicationId, $licenceId)
    {
        return [
            0 => 'data row 1',
            1 => 'data row 2',
        ];
    }

    /**
     * Only here to ensure interface implemented (so phpstan ignored)
     *
     * @phpstan-ignore-next-line
     *
     * @return void
     */
    public function delete(array $ids, $applicationId)
    {
    }
}
