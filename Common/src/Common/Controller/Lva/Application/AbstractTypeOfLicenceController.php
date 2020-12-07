<?php

namespace Common\Controller\Lva\Application;

use Common\Controller\Lva;
use Common\Data\Mapper\Lva\TypeOfLicence as TypeOfLicenceMapper;
use Common\FormService\Form\Lva\TypeOfLicence\AbstractTypeOfLicence as TypeOfLicenceFormService;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence;
use Laminas\Http\Response;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends Lva\AbstractTypeOfLicenceController
{
    /**
     * Application type of licence section
     *
     * @return array|\Common\View\Model\Section|Response
     */
    public function indexAction()
    {
        $prg = $this->prg();

        // If have posted, and need to redirect to get
        if ($prg instanceof Response) {
            return $prg;
        }
        /** @var TypeOfLicenceFormService $tolFormService */
        $tolFormService = $this->getServiceLocator()->get('FormServiceManager')->get('lva-application-type-of-licence');
        /** @var \Laminas\Form\FormInterface $form */
        $form = $tolFormService->getForm();

        // always fetch Application data to check operator location
        $applicationData = $this->getApplicationData($this->getIdentifier());
        if (!$applicationData) {
            return $this->notFoundAction();
        }

        // If we have no data (not posted)
        if ($prg === false) {
            $form->setData(TypeOfLicenceMapper::mapFromResult($applicationData));
            if (isset($applicationData['allowedOperatorLocation'])) {
                $tolFormService->setAndLockOperatorLocation($form, $applicationData['allowedOperatorLocation']);
            }
            return $this->renderIndex($form);
        }

        // If we have posted and have data
        $form->setData($prg);
        if (isset($applicationData['allowedOperatorLocation'])) {
            $tolFormService->setAndLockOperatorLocation($form, $applicationData['allowedOperatorLocation']);
        }

        // If the form is invalid, render the errors
        if (!$form->isValid()) {
            return $this->renderIndex($form);
        }

        $formData = $form->getData();

        $dto = UpdateTypeOfLicence::create(
            [
                'id' => $this->getIdentifier(),
                'version' => $formData['version'],
                'operatorType' => $formData['type-of-licence']['operator-type'],
                'licenceType' => $formData['type-of-licence']['licence-type'],
                'niFlag' => $this->getOperatorLocation($applicationData, $formData)
            ]
        );

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand($dto);

        if ($response->isOk()) {
            return $this->completeSection('type_of_licence', $prg);
        }

        if ($response->isClientError()) {
            // This means we need confirmation
            if (isset($response->getResult()['messages']['AP-TOL-5'])) {
                $query = $formData['type-of-licence'];
                $query['operator-location'] = $this->getOperatorLocation($applicationData, $formData);
                $query['version'] = $formData['version'];

                return $this->redirect()->toRoute(
                    $this->getBaseRoute() . '/action',
                    ['action' => 'confirmation'],
                    ['query' => $query],
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

    /**
     * Get operator location
     *
     * @param array $data     Application or Organisation data
     * @param array $formData Api/Form Data
     *
     * @return string
     */
    protected function getOperatorLocation($data, $formData)
    {
        if (isset($data['allowedOperatorLocation'])) {
            if ($data['allowedOperatorLocation'] === TypeOfLicenceFormService::ALLOWED_OPERATOR_LOCATION_NI) {
                $operatorLocation ='Y';
            } elseif ($data['allowedOperatorLocation'] === TypeOfLicenceFormService::ALLOWED_OPERATOR_LOCATION_GB) {
                $operatorLocation ='N';
            }
            return $operatorLocation;
        }
        return $formData['type-of-licence']['operator-location'];
    }

    /**
     * Handle action - Confirmation
     *
     * @return \Common\View\Model\Section|Response
     */
    public function confirmationAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $query = (array)$this->params()->fromQuery();

            $dto = UpdateTypeOfLicence::create(
                [
                    'id' => $this->getIdentifier(),
                    'version' => $query['version'],
                    'operatorType' => $query['operator-type'],
                    'licenceType' => $query['licence-type'],
                    'niFlag' => $query['operator-location'],
                    'confirm' => true
                ]
            );

            /** @var \Common\Service\Cqrs\Response $response */
            $response = $this->handleCommand($dto);

            if ($response->isOk()) {
                return $this->redirect()->toRouteAjax(
                    'lva-application',
                    ['application' => $response->getResult()['id']['application']]
                );
            }

            $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('unknown-error');

            return $this->redirect()->toRouteAjax(null, ['action' => null], [], true);
        }

        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $form = $formHelper->createForm('GenericConfirmation');
        $formHelper->setFormActionFromRequest($form, $this->getRequest());

        return $this->render(
            'application_type_of_licence_confirmation',
            $form,
            ['sectionText' => 'application_type_of_licence_confirmation_subtitle']
        );
    }
}
