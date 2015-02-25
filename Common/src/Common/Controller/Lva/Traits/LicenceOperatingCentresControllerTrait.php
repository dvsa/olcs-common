<?php

/**
 * Common licence OC controller logic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * Common licence OC controller logic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceOperatingCentresControllerTrait
{
    use CreateVariationTrait;

    public function addAction()
    {
        $form = $this->processForm();

        // If we don't have an instance of Form, it should be a Response object, so we can just return it
        if (!($form instanceof Form)) {
            return $form;
        }

        return $this->render(
            'oc-create-variation-confirmation-title',
            $form,
            ['sectionText' => 'oc-create-variation-confirmation-message']
        );
    }

    public function deleteAction()
    {
        $ids = explode(',', $this->params('child_id'));
        $rows = $this->getAdapter()->getTableData();

        if (count($ids) >= count($rows)) {
            $request = $this->getRequest();

            if ($request->isPost()) {
                return $this->redirect()->toRouteAjax('create_variation', [], [], true);
            }

            $form = $this->getServiceLocator()->get('Helper\Form')
                ->createFormWithRequest('GenericConfirmation', $request);

            $form->get('form-actions')->get('submit')->setLabel('create-variation-button');

            $translator = $this->getServiceLocator()->get('Helper\Translation');

            return $this->render(
                'create-variation-confirmation',
                $form,
                [
                    'sectionText' => $translator->translateReplace(
                        'variation-required-message-prefix',
                        // @todo replace with real link
                        array('#coming-soon')
                    )
                ]
            );
        }

        return parent::deleteAction();
    }
}
