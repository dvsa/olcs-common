<?php
namespace Common\Controller;

interface CrudInterface
{
    public function indexAction();

    public function addAction();

    public function editAction();

    public function deleteAction();
}