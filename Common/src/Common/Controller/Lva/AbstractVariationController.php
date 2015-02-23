<?php

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Abstract Variation Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractVariationController extends AbstractController
{
    public function indexAction()
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
            'create-variation-confirmation',
            $form,
            ['sectionText' => 'licence.variation.confirmation.text']
        );
    }
}
