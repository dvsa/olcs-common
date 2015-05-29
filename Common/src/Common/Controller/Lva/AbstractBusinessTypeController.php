<?php

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;
use Common\Data\Mapper\Lva\BusinessType;
use Dvsa\Olcs\Transfer\Command\Organisation\UpdateBusinessType;
use Dvsa\Olcs\Transfer\Query\Organisation\Organisation;
use Zend\Http\Response;

/**
 * Shared logic between Business type controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractBusinessTypeController extends AbstractController implements AdapterAwareInterface
{
    use Traits\AdapterAwareTrait;

    /**
     * Business type section
     */
    public function indexAction()
    {
        $prg = $this->prg();

        // If have posted, and need to redirect to get
        if ($prg instanceof Response) {
            return $prg;
        }

        $orgId = $this->getCurrentOrganisationId();
        $response = $this->getBusinessType($orgId);

        if (!$response->isOk()) {

            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            return $this->notFoundAction();
        }

        $result = $response->getResult();

        $hasInForceLicences = $result['hasInforceLicences'];

        /** @var \Zend\Form\Form $form */
        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-business_type')
            ->getForm($hasInForceLicences);

        // If we haven't posted
        if ($prg === false) {
            $data = BusinessType::mapFromResult($result);

            $form->setData($data);

            return $this->render('business_type', $form);
        }

        $form->setData($prg);

        if (!$form->isValid()) {
            return $this->render('business_type', $form);
        }

        $data = $form->getData();

        $dto = UpdateBusinessType::create(
            ['id' => $orgId, 'version' => $data['version'], $this->lva => $this->getIdentifier()]
        );

        if (isset($data['data']['type'])) {
            $dto->exchangeArray(['businessType' => $data['data']['type']]);
        }

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if ($response->isOk()) {
            return $this->completeSection('business_type', $prg);
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');

        // We may have a disabled business type element, so we need to fill in the values
        if (empty($data['data']['type'])) {
            $data['data']['type'] = $result['type']['id'];
            $form->setData($data);
        }

        return $this->render('business_type', $form);
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    private function getBusinessType($orgId)
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(Organisation::create(['id' => $orgId]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }
}
