<?php

namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CrudActionTrait;
use Laminas\Mvc\Controller\AbstractActionController;

/**
 * CRUD Action Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudActionTraitStub extends AbstractActionController
{
    use CrudActionTrait;

    public $baseRoute;
    public $lva;

    public function callGetCrudAction(array $formTables = array())
    {
        return $this->getCrudAction($formTables);
    }

    public function callGetActionFromCrudAction($data)
    {
        return $this->getActionFromCrudAction($data);
    }

    public function callHandleCrudAction(
        $data,
        $rowsNotRequired = ['add'],
        $childIdParamName = 'child_id',
        $route = null
    ) {
        return $this->handleCrudAction($data, $rowsNotRequired, $childIdParamName, $route);
    }

    public function callGetBaseRoute()
    {
        return $this->getBaseRoute();
    }
}
