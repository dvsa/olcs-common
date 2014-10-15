<?php

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait CrudTableTrait
{
    /**
     * implementors of this trait *must* support add, edit & delete
     */
    abstract public function addAction();
    abstract public function editAction();
    abstract protected function delete();
    abstract protected function formatCrudDataForSave($data);
    abstract protected function formatCrudDataForForm($data);

    private function handleCrudAction($data)
    {
        $action = strtolower($data['action']);

        if ($action === 'add') {
            $routeParams = array(
                'action' => 'add'
            );
        } else {
            $routeParams = array(
                'action' => $action,
                'child_id' => $data['id']
            );
        }

        return $this->redirect()->toRoute(
            'lva-' . $this->lva . '/' . $this->section,
            $routeParams,
            array(),
            true
        );
    }

    private function handleCrudSave($section)
    {
        // we can't just opt-in to all existing route params because
        // we might have a child ID if we're editing; if so we *don't*
        // want that in the redirect or we'll end up back on the same page
        $routeParams = array(
            'id' => $this->params('id')
        );
        if ($this->isButtonPressed('addAnother')) {
            $routeParams['action'] = 'add';
        }
        return $this->redirect()->toRoute(
            'lva-' . $this->lva . '/' . $this->section,
            $routeParams
        );
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        $request = $this->getRequest();
        $id = $this->params('child_id');

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('GenericDeleteConfirmation')
            ->setData(array('id' => $id));

        if ($request->isPost() && $form->isValid()) {

            $this->delete();

            return $this->redirect()->toRoute(
                'lva-' . $this->lva . '/' . $this->section,
                array('id' => $this->params('id'))
            );
        }
        return $this->render('delete', $form);
    }
}
