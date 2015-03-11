<?php

/**
 * Operating Centre Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Interfaces;

use Zend\Form\Form;
use Zend\Http\Request;

/**
 * Operating Centre Adapter Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface OperatingCentreAdapterInterface extends AdapterInterface
{
    /**
     * Add messages to the main index page
     */
    public function addMessages($id);

    /**
     * Attach the relevant scripts to the main page
     */
    public function attachMainScripts();

    /**
     * Get extra document properties to save
     *
     * @return array
     */
    public function getDocumentProperties();

    /**
     * Get operating centre data
     *
     * @param int $id
     * @return array
     */
    public function getOperatingCentresFormData($id);

    /**
     * Get the table data for the main form
     *
     * @return array
     */
    public function getTableData();

    /**
     * Create a prepared form for the given LVA type
     *
     * @return Zend\Form\Form
     */
    public function getMainForm();

    /**
     * Get an add/edit form based on the mode
     *
     * @param string $mode
     * @param \Zend\Http\Request $request
     * @return type
     */
    public function getActionForm($mode, Request $request);

    /**
     * By default our CRUD validation is to disable everything.
     * However, some LVAs want to extend this behaviour.
     *
     * @param \Zend\Form\Form $form
     */
    public function disableValidation(Form $form);

    /**
     * Callback to populate files
     */
    public function getDocuments();

    /**
     * Format the data for the form
     *
     * @param array $oldData
     * @param string $mode
     * @return array
     */
    public function formatCrudDataForForm(array $oldData, $mode);

    /**
     * Get the child id of the action
     *
     * @return int
     */
    public function getChildId();

    /**
     * Save the main form data
     *
     * @param array $data
     */
    public function saveMainFormData(array $data);

    /**
     * Get address data
     *
     * @param int $id
     * @return array
     */
    public function getAddressData($id);

    /**
     * Save action form data
     *
     * @param string $mode
     * @param array $data
     * @param array $formData
     * @throws \Exception
     */
    public function saveActionFormData($mode, array $data, array $formData);

    /**
     * Delete the selected children
     */
    public function delete();
}
