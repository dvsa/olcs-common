<?php

/**
 * Crud Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Crud\Stubs;

use Common\Controller\Crud\CrudControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Crud Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudControllerTraitStub extends AbstractActionController
{
    use CrudControllerTrait;

    public function callConfirmDelete($crudService, $pageTitle, $sectionText, $id)
    {
        return $this->confirmDelete($crudService, $pageTitle, $sectionText, $id);
    }

    public function callAddOrEditForm($crudService, $pageTitle, $id = null)
    {
        return $this->addOrEditForm($crudService, $pageTitle, $id);
    }

    public function callRenderTable($table, $title = null, $subTitle = null)
    {
        return $this->renderTable($table, $title, $subTitle);
    }

    public function callRenderForm($form, $title = null, $subTitle = null, $params = [])
    {
        return $this->renderForm($form, $title, $subTitle, $params);
    }
}
