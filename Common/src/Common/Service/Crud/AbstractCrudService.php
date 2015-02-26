<?php

/**
 * Abstract Crud Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Zend\Form\Form;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Abstract Crud Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractCrudService implements CrudServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Handle an individual deletion
     *
     * @param int $id
     */
    abstract protected function delete($id);

    /**
     * Check if a form is valid
     *
     * @NOTE This method is used by genericCrudService to allow you to add aditional custom validation checks before
     * processing the save
     *
     * @param Form $form
     * @return boolean
     */
    public function isFormValid(Form $form, $id = null)
    {
        return $form->isValid();
    }

    /**
     * Get default form data
     *
     * @NOTE This method is used by genericCrudService to allow you to define default form data
     *
     * @return array
     */
    public function getDefaultFormData()
    {
        return [];
    }

    /**
     * Get the delete confirmation form
     *
     * @param Request $request
     */
    public function getDeleteForm(Request $request)
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('GenericDeleteConfirmation', $request);
    }

    /**
     * Process deletions
     *
     * @param array $ids
     */
    public function processDelete(array $ids = [])
    {
        foreach ($ids as $id) {
            $this->delete($id);
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addSuccessMessage('record-deleted');

        $redirect = new Redirect();
        return $redirect->toRouteAjax(null);
    }

    /**
     * Process an Add/Edit form
     *
     * @param Request $request
     * @param int $id
     */
    public function processForm(Request $request, $id = null)
    {
        return $this->getServiceLocator()->get('Crud\Generic')->processForm($this, $request, $id);
    }
}
