<?php

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Http\Response;

/**
 * Crud table trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CrudTableTrait
{
    use CrudActionTrait;

    /**
     * Once the CRUD entity has been saved, handle the necessary redirect
     *
     * @param string $prefix - if our actions aren't just 'add', 'edit', provide a prefix
     */
    protected function handlePostSave($prefix = null)
    {
        // we can't just opt-in to all existing route params because
        // we might have a child ID if we're editing; if so we *don't*
        // want that in the redirect or we'll end up back on the same page
        $routeParams = array(
            $this->getIdentifierIndex() => $this->getIdentifier()
        );

        if ($this->isButtonPressed('addAnother')) {
            $action = $prefix !== null ? $prefix . '-add' : 'add';
            $routeParams['action'] = $action;
            $method = 'toRoute';
        } else {
            $method = 'toRouteAjax';
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage(
            'section.' . $this->params('action') . '.' . $this->section
        );

        return $this->redirect()->$method(null, $routeParams);
    }

    /**
     * Generic delete functionality; usually does the trick but
     * can be overridden if not
     */
    public function deleteAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $response = $this->delete();

            if ($response instanceof Response) {
                return $response;
            }

            if ($response === false) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage(
                    'section.' . $this->params('action') . '.' . $this->section . '-failed'
                );
            } else {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage(
                    'section.' . $this->params('action') . '.' . $this->section
                );
            }

            return $this->redirect()->toRouteAjax(
                null,
                array($this->getIdentifierIndex() => $this->getIdentifier())
            );
        }

        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericDeleteConfirmation', $request);

        $params = ['sectionText' => $this->getDeleteMessage()];

        return $this->render($this->getDeleteTitle(), $form, $params);
    }

    /**
     * This method needs to exists for deleteAction to work, the method should be overidden, but cannot be declared
     * abstract as it's not always required, so by default we throw an exception
     *
     * @throws \BadMethodCallException
     */
    protected function delete()
    {
        throw new \BadMethodCallException('Delete method must be implemented');
    }

    /**
     * Which delete message to use.
     *
     * @return string The modal message key.
     */
    protected function getDeleteMessage()
    {
        return 'delete.confirmation.text';
    }

    /**
     * Which delete title to use.
     *
     * @return string The modal message key.
     */
    protected function getDeleteTitle()
    {
        return 'delete';
    }
}
