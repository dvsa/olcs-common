<?php

namespace Common\Controller;

use Common\Form\Model\Form\Test\AllElements;
use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Laminas\View\Model\ViewModel;

class FormTestController extends AbstractOlcsController
{
    public function __construct(private FormServiceManager $formServiceManager, private FormHelperService $formHelperService) {}

    public function indexAction()
    {
        $form = $this->formHelperService->createForm(AllElements::class);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData((array) $request->getPost());
            $form->isValid();
        }

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('partials/form');
        return $view;
    }
}
