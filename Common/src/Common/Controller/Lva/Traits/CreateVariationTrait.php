<?php

/**
 * Create Variation Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

/**
 * Create Variation Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait CreateVariationTrait
{
    protected function processForm()
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

        return $form;
    }
}
