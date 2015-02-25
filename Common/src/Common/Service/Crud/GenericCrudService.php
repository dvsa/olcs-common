<?php

/**
 * Generic Crud Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Crud;

use Common\Util\Redirect;
use Common\Exception\ResourceConflictException;
use Zend\Http\Request;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Generic Crud Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenericCrudService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Process an Add/Edit form
     *
     * @param object $service
     * @param Request $request
     * @param int $id
     */
    public function processForm(GenericProcessFormInterface $service, Request $request, $id = null)
    {
        // If we have posted the form, we want to check if the form is valid
        if ($request->isPost()) {

            $form = $this->getForm($service, $request)->setData((array)$request->getPost());

            // If the form is valid, we want to process save
            if ($form->isValid()) {

                try {
                    return $service->processSave($form->getData(), $id);
                } catch (ResourceConflictException $ex) {
                    // If we have a version conflict, we want to add a flash message and skip a response at this stage
                    // so the form resets
                    $this->getServiceLocator()->get('Helper\FlashMessenger')
                        ->addErrorMessage('version-conflict-message');
                }
            } else {
                return $form;
            }
        }

        // If we haven't posted the form, and we have an id
        // We want to grab the records data and populate the form
        if (isset($id)) {

            $data = $service->getRecordData($id);

            // If data is null, then we haven't found a record
            if ($data === null) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('record-not-found');

                $redirect = new Redirect();
                return $redirect->toRoute(null);
            }

            return $this->getForm($service, $request)->setData($data);
        }

        $form = $this->getForm($service, $request);

        if (method_exists($service, 'getDefaultFormData')) {
            $form->setData($service->getDefaultFormData());
        }

        return $form;
    }

    protected function getForm($service, $request)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $service->getForm();

        $formHelper->setFormActionFromRequest($form, $request);

        return $form;
    }
}
