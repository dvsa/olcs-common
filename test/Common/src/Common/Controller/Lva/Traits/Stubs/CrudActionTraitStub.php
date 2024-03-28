<?php

namespace CommonTest\Common\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CrudActionTrait;
use Common\Service\Helper\FlashMessengerHelperService;
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

    public $flashMessengerHelper;

    public function __construct(FlashMessengerHelperService $flashMessengerHelper)
    {
        $this->flashMessengerHelper = $flashMessengerHelper;
    }


    public function callGetCrudAction(array $formTables = [])
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
