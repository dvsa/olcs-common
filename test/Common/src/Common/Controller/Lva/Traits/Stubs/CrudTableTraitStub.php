<?php

namespace CommonTest\Common\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\CrudTableTrait;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\FormHelperService;
use Laminas\Mvc\Controller\AbstractActionController;

/**
 * CRUD Table Trait Stub
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class CrudTableTraitStub extends AbstractActionController
{
    /**
     * @var \Common\Service\Helper\FlashMessengerHelperService
     */
    public $flashMessengerHelper;
    /**
     * @var \Common\Service\Helper\FormHelperService
     */
    public $formHelper;
    use CrudTableTrait;

    protected $section = 'fake-section';

    public function __construct(FlashMessengerHelperService $flashMessengerHelper, FormHelperService $formHelper)
    {
        $this->flashMessengerHelper = $flashMessengerHelper;
        $this->formHelper = $formHelper;
    }

    public function callHandlePostSave($prefix = null, $options = [])
    {
        return $this->handlePostSave($prefix, $options);
    }
}
