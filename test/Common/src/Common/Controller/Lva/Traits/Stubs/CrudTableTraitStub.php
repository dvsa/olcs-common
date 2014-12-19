<?php

/**
 * CRUD Table Trait Stub
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CrudTableTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * CRUD Table Trait Stub
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CrudTableTraitStub extends AbstractActionController
{
    use CrudTableTrait;

    protected $section = 'fake-section';

    public function callHandlePostSave($prefix = null)
    {
        return $this->handlePostSave($prefix);
    }
}
