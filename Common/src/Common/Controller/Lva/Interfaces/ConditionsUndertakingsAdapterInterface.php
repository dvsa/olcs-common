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
}
