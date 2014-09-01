<?php

namespace Common\Controller;

/**
 * Crud interface
 */
interface CrudInterface
{
    public function indexAction();

    public function addAction();

    public function editAction();

    public function deleteAction();
}
