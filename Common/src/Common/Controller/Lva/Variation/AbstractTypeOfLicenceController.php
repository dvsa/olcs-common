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
use Common\RefData;

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
            'currentLicenceType' => $data['currentLicenceType']
        ];

        $tolFormService = $this->getServiceLocator()->get('FormServiceManager')->get('lva-variation-type-of-licence');
        $form = $tolFormService->getForm($params);

        // If we have no data (not posted)
        if ($prg === false) {
            $form->setData(TypeOfLicenceMapper::mapFromResult($data));
            $this->alterForm($form);

            return $this->renderIndex($form);
        }

        // If we have posted and have data
        $form->setData($prg);
        $this->alterForm($form);

        $tolFormService->maybeAlterFormForGoodsStandardInternational($form);

        // If the form is invalid, render the errors
        if (!$form->isValid()) {
            return $this->renderIndex($form);
        }

        $formData = $form->getData();

        $dtoData = [
            'id' => $this->getIdentifier(),
            'version' => $formData['version'],
            'licenceType' => $formData['type-of-licence']['licence-type']['licence-type']
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

    /**
     * Hard code vehicle type to mixed and disable element (a variation changing a licence to standard international
     * can only change to standard international/mixed)
     *
     * @param mixed $form
     */
    private function alterForm($form)
    {
        $vehicleTypeElement = $form->get('type-of-licence')
            ->get('licence-type')
            ->get('ltyp_siContent')
            ->get('vehicle-type');

        $vehicleTypeElement->setAttribute('disabled', 'disabled');
        $vehicleTypeElement->setValue(RefData::APP_VEHICLE_TYPE_MIXED);
    }
}
