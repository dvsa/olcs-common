<?php

/**
 * Crud Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Zend\Http\Request;

/**
 * Crud Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface CrudServiceInterface
{
    /**
     * Get the delete confirmation form
     *
     * @param Request $request
     */
    public function getDeleteForm(Request $request);

    /**
     * Process deletions
     *
     * @param array $ids
     */
    public function processDelete(array $ids = []);

    /**
     * Process an Add/Edit form
     *
     * @param Request $request
     * @param int $id
     */
    public function processForm(Request $request, $id = null);
}
