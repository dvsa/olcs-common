<?php

/**
 * Abstract Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Controller\Lva;

use Zend\Form\Form;
use Common\Service\Entity\LicenceEntityService as Licence;
use Common\Service\Entity\ApplicationEntityService as Application;

/**
 * Abstract Undertakings Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractUndertakingsController extends AbstractController
{
    /**
     *  Undertakings section
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $applicationData = $this->getUndertakingsData();
        $form = $this->getForm();
        $this->updateForm($form, $applicationData);

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $this->save($form->getData());
                return $this->completeSection('undertakings');
            } else {
                // validation failed, we need to use the application data
                // for markup but use the POSTed values to render the form again
                $formData = array_replace_recursive(
                    $this->formatDataForForm($applicationData),
                    $data
                );
                // don't call setData again here or we lose validation messages
                $form->populateValues($formData);
            }
        } else {
            $data = $this->formatDataForForm($applicationData);
            $form->setData($data);
        }

        return $this->render('undertakings', $form);
    }

    /**
     * Get undertakings form
     *
     * This should really be declared abstract - it *should* be overridden
     * by concrete subclasses but we unit test the abstract logic above
     * and mock this method
     *
     * @return \Zend\Form\Form
     */
    protected function getForm()
    {
        return $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-undertakings')
            ->getForm();
    }

    protected function formatDataForForm($applicationData)
    {
        // override in concrete classes if required
        return $applicationData;
    }

    /**
     * @inheritdoc
     */
    protected function updateForm($form, $data)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $summaryDownload = $translator->translateReplace(
            'undertakings_summary_download',
            [
                $this->url()->fromRoute('lva-' . $this->lva . '/review', [], [], true),
                $translator->translate('view-full-application'),
            ]
        );

        $form->get('declarationsAndUndertakings')->get('summaryDownload')->setAttribute('value', $summaryDownload);
    }

    /**
     * Get Application Data
     *
     * @return array|false
     */
    protected function getUndertakingsData()
    {
        $query = $this->getServiceLocator()->get('TransferAnnotationBuilder')
            ->createQuery(\Dvsa\Olcs\Transfer\Query\Application\Declaration::create(['id' => $this->getIdentifier()]));

        $response =  $this->getServiceLocator()->get('QueryService')->send($query);

        if ($response->isOk()) {
            return $response->getResult();
        }

        $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');

        return false;
    }

    /**
     * Save the form data
     *
     * @param array $formData
     */
    protected function save($formData)
    {
        $dto = $this->createUpdateDeclarationDto($formData);

        $command = $this->getServiceLocator()->get('TransferAnnotationBuilder')->createCommand($dto);

        /* @var $response \Common\Service\Cqrs\Response */
        $response = $this->getServiceLocator()->get('CommandService')->send($command);

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
        }
    }

    protected function createUpdateDeclarationDto($formData)
    {
        $dto = \Dvsa\Olcs\Transfer\Command\Application\UpdateDeclaration::create(
            [
                'id' => $this->getIdentifier(),
                'version' => $formData['declarationsAndUndertakings']['version'],
                'declarationConfirmation' => $formData['declarationsAndUndertakings']['declarationConfirmation'],
                'interimRequested' => isset($formData['interim']) ?
                    $formData['interim']['goodsApplicationInterim'] : null,
                'interimReason' => isset($formData['interim']) ?
                    $formData['interim']['goodsApplicationInterimReason'] : null,
            ]
        );

        return $dto;
    }

    /**
     * @param string $goodsOrPsv
     * @return string
     */
    protected function getPartialPrefix($goodsOrPsv)
    {
        if ($goodsOrPsv === Licence::LICENCE_CATEGORY_PSV) {
            return 'psv';
        }

        return 'gv';
    }
}
