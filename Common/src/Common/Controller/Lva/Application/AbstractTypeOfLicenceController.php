<?php

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Application;

use Common\Controller\Lva\AbstractController;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Transfer\Query\Application\Application;
use Common\Controller\Lva;
use Zend\Http\Response;
use Common\Data\Mapper\Lva\TypeOfLicence as TypeOfLicenceMapper;
use Zend\Form\Form;

/**
 * Common Lva Abstract Type Of Licence Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTypeOfLicenceController extends AbstractController
{
    /**
     * Application type of licence section
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

        $form = $this->getServiceLocator()->get('FormServiceManager')
            ->get('lva-application-type-of-licence')
            ->getForm();

        // If we have no data (not posted)
        if ($prg === false) {

            $response = $this->getTypeOfLicence();

            if ($response->isNotFound()) {
                return $this->notFoundAction();
            }

            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            if ($response->isOk()) {
                $mapper = new TypeOfLicenceMapper();
                $form->setData($mapper->mapFromResult($response->getResult()));
            }

            return $this->renderIndex($form);
        }

        // If we have posted and have data
        $form->setData($prg);

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
                'niFlag' => $formData['type-of-licence']['operator-location']
            ]
        );

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /** @var \Common\Service\Cqrs\Response $response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if ($response->isOk()) {
            return $this->completeSection('type_of_licence', $prg);
        }

        if ($response->isClientError()) {

            // This means we need confirmation
            if (isset($response->getResult()['messages']['AP-TOL-5'])) {

                $query = $formData['type-of-licence'];
                $query['version'] = $formData['version'];

                return $this->redirect()->toRoute(
                    null,
                    ['action' => 'confirmation'],
                    ['query' => $query],
                    true
                );
            }

            $this->mapErrors($form, $response->getResult()['messages']);
        }

        if ($response->isServerError()) {
            var_dump($response->getResult()['messages']);
            exit;
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }

        return $this->renderIndex($form);
    }

    /**
     * @return \Common\Service\Cqrs\Response
     */
    protected function getTypeOfLicence()
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(Application::create(['id' => $this->getIdentifier()]));

        return $this->getServiceLocator()->get('QueryService')->send($query);
    }

    protected function renderIndex($form)
    {
        $this->getServiceLocator()->get('Script')->loadFile('type-of-licence');

        return $this->render('type_of_licence', $form);
    }

    protected function mapErrors(Form $form, array $errors)
    {
        $formMessages = [];

        if (isset($errors['licenceType'])) {

            foreach ($errors['licenceType'][0] as $key => $message) {
                $formMessages['type-of-licence']['licence-type'][] = $key;
            }

            unset($errors['licenceType']);
        }

        if (isset($errors['goodsOrPsv'])) {

            foreach ($errors['goodsOrPsv'][0] as $key => $message) {
                $formMessages['type-of-licence']['operator-type'][] = $key;
            }

            unset($errors['licenceType']);
        }

        // @todo might need tweaking
        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
