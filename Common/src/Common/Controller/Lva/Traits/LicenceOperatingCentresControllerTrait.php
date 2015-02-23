<?php

/**
 * Common licence OC controller logic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Common licence OC controller logic
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait LicenceOperatingCentresControllerTrait
{

    public function addAction()
    {
        // @NOTE The behaviour of this service differs internally to externally
        $processingService = $this->getServiceLocator()->get('Processing\CreateVariation');

        $request = $this->getRequest();

        $form = $processingService->getForm($request);

        if ($request->isPost() && $form->isValid()) {

            $data = $processingService->getDataFromForm($form);

            $licenceId = $this->params('licence');

            $appId = $processingService->createVariation($licenceId, $data);

            return $this->redirect()->toRouteAjax('lva-variation', ['application' => $appId]);
        }

        return $this->render(
            'oc-create-variation-confirmation-title',
            $form,
            array('sectionText' => 'oc-create-variation-confirmation-message')
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
