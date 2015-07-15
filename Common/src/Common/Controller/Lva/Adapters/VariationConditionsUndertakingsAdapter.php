<?php

/**
 * Variation Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Common\Service\Table\TableBuilder;

/**
 * Variation Conditions Undertakings Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakingsAdapter extends AbstractConditionsUndertakingsAdapter
{
    protected $tableName = 'lva-variation-conditions-undertakings';

    const ACTION_ADDED = 'A'; // Record added to the application
    const ACTION_EXISTING = 'E'; // Unchanged record against the licence
    const ACTION_CURRENT = 'C'; // Current version of record updated on the application
    const ACTION_UPDATED = 'U'; // Record updated on the application
    const ACTION_DELETED = 'D'; // Record deleted on the application
    const ACTION_REMOVED = 'R'; // The corresponding licence record, to the delta delete record

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts()
    {
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');
    }

    /**
     * Check whether we can update the record
     *
     * @param int $id
     * @return bool
     */
    public function canEditRecord($data)
    {
        if (!isset($data['action'])) {
            return true;
        }
        return in_array($data['action'], ['', self::ACTION_ADDED, self::ACTION_EXISTING, self::ACTION_UPDATED]);
    }

    /**
     * Remove the restore button
     *
     * @param TableBuilder $table
     */
    public function alterTable(TableBuilder $table)
    {
        // prevent PMD error
        unset($table);
    }

    /**
     * Get the command to delete
     *
     * @param array  $ids List of ConditionUndertaking ID to delete
     *
     * @return \Dvsa\Olcs\Transfer\Command\ConditionUndertaking\DeleteList
     */
    public function getDeleteCommand($id, $ids)
    {
        return \Dvsa\Olcs\Transfer\Command\Variation\DeleteListConditionUndertaking::create(
            ['id' => $id, 'ids' => $ids]
        );
    }

    /**
     * Get the command to update
     *
     * @param array $formData Form data
     * @param int   $id Application ID
     *
     * @return \Dvsa\Olcs\Transfer\Command\ConditionUndertaking\Update
     */
    public function getUpdateCommand($formData, $id)
    {
        $data = $this->processDataForSave($formData, null);
        $params = [
            'id' => $id,
            'conditionUndertaking' => $data['fields']['id'],
            'version' => $data['fields']['version'],
            'type' => $data['fields']['conditionType'],
            'notes' => $data['fields']['notes'],
            'fulfilled' => $data['fields']['isFulfilled'],
            'attachedTo' => $data['fields']['attachedTo'],
            'operatingCentre' => $data['fields']['operatingCentre'],
        ];

        return \Dvsa\Olcs\Transfer\Command\Variation\UpdateConditionUndertaking::create($params);
    }
}
