<?php

/**
 * Generic Process Form Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

/**
 * Generic Process Form Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface GenericProcessFormInterface
{
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
