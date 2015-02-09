<?php

/**
 * Conditions Undertakings Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

use Zend\Form\Form;

/**
 * Conditions Undertakings Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ConditionsUndertakingsAdapterInterface
{
    /**
     * Delete a record
     *
     * @param int $id
     * @param int $parentId
     */
    public function delete($id, $parentId);

    /**
     * Check whether we can update the record
     *
     * @param int $id
     * @param int $parentId
     * @return bool
     */
    public function canEditRecord($id, $parentId);

    /**
     * Alter the form based upon the id
     *
     * @param Form $form
     * @param int $id
     */
    public function alterForm(Form $form, $id);

    /**
     * Save the data
     *
     * @param array $data
     * @return int
     */
    public function save($data);

    /**
     * Get the table data
     *
     * @param int $id
     * @return array
     */
    public function getTableData($id);

    /**
     * Process the data for saving
     *
     * @param array $data
     * @param int $id
     * @return array
     */
    public function processDataForSave($data, $id);

    /**
     * Process the data for the form
     *
     * @param array $data
     * @return array
     */
    public function processDataForForm($data);
}
