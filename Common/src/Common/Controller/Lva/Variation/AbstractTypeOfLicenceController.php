<?php

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Variation;

use Dvsa\Olcs\Transfer\Command\Variation\UpdateTypeOfLicence;
use Dvsa\Olcs\Transfer\Query\Variation\TypeOfLicence;
use Common\Controller\Lva;
use Laminas\Http\Response;
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

        if ($response->isClientError() || $response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            return $this->notFoundAction();
        }

        $data = $response->getResult();

        $params = [
            'canBecomeSpecialRestricted' => $data['canBecomeSpecialRestricted'],
            'canUpdateLicenceType' => $data['canUpdateLicenceType'],
            'currentLicenceType' => $data['currentLicenceType'],
            'currentVehicleType' => $data['currentVehicleType']
        ];

        $tolFormService = $this->getServiceLocator()->get('FormServiceManager')->get('lva-variation-type-of-licence');
        $form = $tolFormService->getForm($params);

        $mappedData = TypeOfLicenceMapper::mapFromResult($data);

        // If we have no data (not posted)
        if ($prg === false) {
            $form->setData($mappedData);

            return $this->renderIndex($form);
        }

        // If we have posted and have data
        // manually set operator location and type data in form as the fields are disabled on submission
        $prg['type-of-licence']['operator-location'] = $mappedData['type-of-licence']['operator-location'];
        $prg['type-of-licence']['operator-type'] = $mappedData['type-of-licence']['operator-type'];
        $form->setData($prg);

        $tolFormService->maybeAlterFormForGoodsStandardInternational($form);

        // If the form is invalid, render the errors
        if (!$form->isValid()) {
            return $this->renderIndex($form);
        }

        $formData = $form->getData();
        $licenceTypeData = $formData['type-of-licence']['licence-type'];

        $vehicleType = null;
        $lgvDeclarationConfirmation = 0;

        if (isset($licenceTypeData['ltyp_siContent'])) {
            $siContentData = $licenceTypeData['ltyp_siContent'];
            $vehicleType = $siContentData['vehicle-type'];

            if (isset($siContentData['lgv-declaration']['lgv-declaration-confirmation'])) {
                $lgvDeclarationConfirmation = $siContentData['lgv-declaration']['lgv-declaration-confirmation'];
            }
        }

        $dtoData = [
            'id' => $this->getIdentifier(),
            'version' => $formData['version'],
            'licenceType' => $formData['type-of-licence']['licence-type']['licence-type'],
            'vehicleType' => $vehicleType,
            'lgvDeclarationConfirmation' => $lgvDeclarationConfirmation,
        ];

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->handleCommand(UpdateTypeOfLicence::create($dtoData));

        if ($response->isOk()) {
            return $this->completeSection('type_of_licence', $prg);
        }

        if ($response->isClientError()) {
            $this->mapErrors($form, $response->getResult()['messages']);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return $this->renderIndex($form);
    }
}
