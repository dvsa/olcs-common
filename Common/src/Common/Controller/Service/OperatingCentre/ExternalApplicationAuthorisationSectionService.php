<?php

/**
 * External Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

/**
 * External Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalApplicationAuthorisationSectionService extends AbstractApplicationAuthorisationSectionService
{
    /**
     * This is called from the summary section, in this instance it just wraps getFormTableData
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    public function getSummaryTableData($id, $table)
    {
        return $this->getFormTableData($id, $table);
    }
}
