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
     * Process an Add/Edit form
     *
     * @param Request $request
     * @param int $id
     */
    public function processForm(Request $request, $id = null);
}
