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
            'currentLicenceType' => $data['currentLicenceType']
        ];

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-variation-type-of-licence')
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
            $this->mapErrors($form, $response->getResult()['messages']);
        }

        if ($response->isServerError()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return $this->renderIndex($form);
    }
}
