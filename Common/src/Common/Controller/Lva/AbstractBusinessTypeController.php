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
        if ($this->getRequest()->isPost() === false) {
            $data = BusinessType::mapFromResult($result);

            $form->setData($data);

            return $this->render('business_type', $form);
        }

        $postData = (array)$this->getRequest()->getPost();

        // If this is set, then we have confirmed
        if (isset($postData['custom'])) {
            $dtoData = json_decode($postData['custom'], true);
            $dtoData['confirm'] = true;
        } else {
            $form->setData($postData);

            if (!$form->isValid()) {
                return $this->render('business_type', $form);
            }

            $data = $form->getData();

            $dtoData = [
                'id' => $orgId,
                'version' => $data['version'],
                $this->lva => $this->getIdentifier(),
                'confirm' => false
            ];

            if (isset($data['data']['type'])) {
                $dtoData['businessType'] = $data['data']['type'];
            }
        }

        $dto = UpdateBusinessType::create($dtoData);

        $response = $this->handleCommand(UpdateBusinessType::create($dtoData));

        if ($response->isOk()) {
            return $this->completeSection('business_type', $postData);
        }

        $messages = $response->getResult()['messages'];

        if (isset($messages['BUS_TYP_REQ_CONF'])) {
            $transitions = json_decode($messages['BUS_TYP_REQ_CONF']);

            $labels = [];

            $translation = $this->getServiceLocator()->get('Helper\Translation');

            foreach ($transitions as $transition) {
                $labels[] = $translation->translate($transition);
            }

            $label = $translation->translateReplace('BUS_TYP_REQ_CONF', [implode('', $labels)]);

            $view = $this->confirm($label, $this->getRequest()->isXmlHttpRequest(), json_encode($dto->getArrayCopy()));
            $view->setTerminal(false);

            $this->placeholder()->setPlaceholder('contentTitle', 'Business type change');
            return $this->viewBuilder()->buildView($view);
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');

        // We may have a disabled business type element, so we need to fill in the values
        if (empty($data['data']['type'])) {
            $data['data']['type'] = $result['type']['id'];
            $form->setData($data);
        }

        return $this->render('business_type', $form);
    }

    public function getForm($form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        return $formHelper->createFormWithRequest($form, $this->getRequest());
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
