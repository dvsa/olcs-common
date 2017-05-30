<?php

namespace Common\Controller\Continuation;

use Common\Controller\Lva\AbstractController;
use Common\Form\Form;
use Zend\View\Model\ViewModel;

/**
 * AbstractContinuationController
 */
abstract class AbstractContinuationController extends AbstractController
{
    /**
     * Get the ViewModel used for continuations
     *
     * @param string    $licNo     Licence number eg OB1234567
     * @param Form|null $form      Form to display on the page
     * @param array     $variables additional variables to view
     *
     * @return ViewModel
     */
    protected function getViewModel($licNo, Form $form = null, $variables = [])
    {
        $view = new ViewModel(
            array_merge(['licNo' => $licNo, 'form' => $form], $variables)
        );

        $view->setTemplate('pages/continuation');

        return $view;
    }

    /**
     * Get a form
     *
     * @param string $formClass Class name of the form to generate
     *
     * @return Form
     */
    protected function getForm($formClass)
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm($formClass);
    }

    /**
     * Get the continuation detail ID
     *
     * @return int
     */
    protected function getContinuationDetailId()
    {
        return (int)$this->params('continuationDetailId');
    }
}
