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

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $form = $this->getForm()->setData($data);
            if ($form->isValid()) {
                $this->save($this->formatDataForSave($data));
                $this->postSave('undertakings');
                $this->handleFees($data);
                return $this->completeSection('undertakings');
            } else {
                // validation failed, we need to lookup application data
                // but use the POSTed checkbox value to render the form again
                $confirmed = $data['declarationsAndUndertakings']['declarationConfirmation'];
                $applicationData = $this->getUndertakingsData();
                $data = $this->formatDataForForm($applicationData);
                $data['declarationsAndUndertakings']['declarationConfirmation'] = $confirmed;
                $form->setData($data);
            }
        } else {
            $applicationData = $this->getUndertakingsData();
            $data = $this->formatDataForForm($applicationData);
            $form = $this->getForm()->setData($data);
        }

        $this->updateForm($form, $applicationData);

        return $this->render('undertakings', $form);
    }

    /**
     * Handle any fees that may need to bo applied upon completing this section.
     *
     * @param $data
     */
    public function handleFees($data)
    {
        if (!isset($data['interim'])) {
            return; // interim not relevant on internal
        }

        $interimService = $this->getServiceLocator()->get('Helper\Interim');

        if ($data['interim']['goodsApplicationInterim'] === 'Y') {
            $interimService->createInterimFeeIfNotExist($data['declarationsAndUndertakings']['id']);
        } elseif ($data['interim']['goodsApplicationInterim'] === 'N') {
            $interimService->cancelInterimFees($data['declarationsAndUndertakings']['id']);
        }
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
        // no-op, override in concrete classes
    }

    protected function formatDataForForm($applicationData)
    {
        // override in concrete classes if required
        return $applicationData;
    }

    protected function updateForm($form, $data)
    {
        $translator = $this->getServiceLocator()->get('Helper\Translation');

        $summaryDownload = sprintf(
            '<p><a href="%s" target="_blank">%s</a></p>',
            $this->url()->fromRoute('lva-' . $this->lva . '/review', [], [], true),
            $translator->translate('view-full-application')
        );

        $form->get('declarationsAndUndertakings')->get('summaryDownload')->setAttribute('value', $summaryDownload);
    }

    protected function formatDataForSave($data)
    {
        return $data['declarationsAndUndertakings'];
    }

    /**
     * Get Application Data
     *
     * @return array
     */
    protected function getUndertakingsData()
    {
        return $this->getServiceLocator()->get('Entity\Application')
            ->getDataForUndertakings($this->getApplicationId());
    }

    /**
     * Save the form data
     *
     * @param array $data
     */
    protected function save($data)
    {
        return $this->getServiceLocator()->get('Entity\Application')->save($data);
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
