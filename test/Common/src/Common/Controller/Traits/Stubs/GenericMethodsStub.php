<?php

namespace CommonTest\Common\Controller\Traits\Stubs;

use Common\Controller\Traits;
use Common\Service\Helper\FormHelperService;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class GenericMethodsStub
{
    use Traits\GenericMethods;

    /**
     * @var \Common\Service\Helper\FormHelperService
     */
    public $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }
}
