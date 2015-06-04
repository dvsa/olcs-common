<?php

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Licence;

use Dvsa\Olcs\Transfer\Command\Licence\UpdateTypeOfLicence;
use Common\Controller\Lva;
use Dvsa\Olcs\Transfer\Query\Licence\TypeOfLicence;
use Zend\Http\Response;
use Common\Data\Mapper\Lva\TypeOfLicence as TypeOfLicenceMapper;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends Lva\AbstractTypeOfLicenceController
{
    /**
     * Licence type of licence section
     *
     * @return Response
     */
    public function indexAction()
    {
        $prg = $this->prg();

        // If have posted, and need to redirect to get
        if ($prg instanceof Response) {
            return $prg;
        }

        $response = $this->handleQuery(TypeOfLicence::create(['id' => $this->getIdentifier()]));

        if ($response->isNotFound()) {
            return $this->notFoundAction();
        }

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            return $this->notFoundAction();
        }

        $data = $response->getResult();

        if (!$data['canUpdateLicenceType']) {
            $this->getServiceLocator()->get('Helper\Guidance')->append('variation-application-text');
        }

        if ($data['doesChangeRequireVariation']) {
            $this->getServiceLocator()->get('Lva\Variation')->addVariationMessage($this->getIdentifier());
        }

        $params = [
            'canBecomeSpecialRestricted' => $data['canBecomeSpecialRestricted'],
            'canUpdateLicenceType' => $data['canUpdateLicenceType']
        ];

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-licence-type-of-licence')
            ->getForm($params);

        // If we have no data (not posted)
        if ($prg === false) {

            $form->setData(TypeOfLicenceMapper::mapFromResult($data));

            return $this->renderIndex($form);
        }

        // If we have posted and have data
        $form->setData($prg);

        // If the form is invalid, render the errors
        if (!$form->isValid()) {
            return $this->renderIndex($form);
        }

        $formData = $form->getData();

        $dtoData = [
            'id' => $this->getIdentifier(),
            'version' => $formData['version'],
            'licenceType' => $formData['type-of-licence']['licence-type']
        ];

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand(UpdateTypeOfLicence::create($dtoData));

        if ($response->isOk()) {
            return $this->completeSection('type_of_licence', $prg);
        }

        if ($response->isClientError()) {

            // This means we need confirmation
            if (isset($response->getResult()['messages']['LIC-REQ-VAR'])) {

                return $this->redirect()->toRoute(
                    null,
                    ['action' => 'confirmation'],
                    ['query' => ['licence-type' => $formData['type-of-licence']['licence-type']]],
                    true
                );
            }

            $this->mapErrors($form, $response->getResult()['messages']);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return $this->renderIndex($form);
    }

    public function confirmationAction()
    {
        // @NOTE The behaviour of this service differs internally to externally
        $processingService = $this->getServiceLocator()->get('Processing\CreateVariation');

        $request = $this->getRequest();

        $form = $processingService->getForm($request);

        if ($request->isPost() && $form->isValid()) {

            $data = $processingService->getDataFromForm($form);

            $data['licenceType'] = $this->params()->fromQuery('licence-type');

            $licenceId = $this->params('licence');

            // @todo migrate this to business
            $appId = $processingService->createVariation($licenceId, $data);
            $this->getServiceLocator()->get('Processing\VariationSection')
                ->setApplicationId($appId)
                ->completeSection('type_of_licence');

            return $this->redirect()->toRouteAjax('lva-variation', ['application' => $appId]);
        }

        return $this->render(
            'create-variation-confirmation',
            $form,
            ['sectionText' => 'licence_type_of_licence_confirmation']
        );
    }
}
