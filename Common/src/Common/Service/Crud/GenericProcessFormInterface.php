<?php

/**
 * Generic Process Form Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Zend\Form\Form;

/**
 * Generic Process Form Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface GenericProcessFormInterface
{
    /**
     * Check if a form is valid
     *
     * @param Form $form
     * @return boolean
     */
    public function isFormValid(Form $form, $id = null);

    /**
     * Get default form data
     *
     * @NOTE This method is used by genericCrudService to allow you to define default form data
     *
     * @return array
     */
    public function getDefaultFormData();

    /**
     * Process the saving of an entity
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    public function processSave($data, $id = null);

    /**
     * Get an entities data by an id
     *
     * @param int $id
     * @return array|null
     */
    public function getRecordData($id);

    /**
     * Grab the built/configured form
     *
     * @return \Zend\Form\Form
     */
    public function getForm();
}
