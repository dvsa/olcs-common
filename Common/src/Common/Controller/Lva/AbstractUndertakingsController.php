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

        $applicationData = $this->getUndertakingsData();

        // $applicationData['licenceType']['id'] = 'ltyp_r';
        // $applicationData['goodsOrPsv']['id']  = 'lcat_gv';
        // $applicationData['niFlag']            = 'N';

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($applicationData);
        }

        $form = $this->getForm()->setData($data);

        $this->updateForm($form, $applicationData);

        if ($request->isPost()) {
            if ($form->isValid()) {
                $this->save($this->formatDataForSave($data));
                $this->postSave('undertakings');
                return $this->completeSection('undertakings');
            } else {
                // validation failed, we need to lookup application data
                // but use the POSTed checkbox value to render the form again
                $confirmed = $data['declarationsAndUndertakings']['declarationConfirmation'];
                $data = $this->formatDataForForm($this->getUndertakingsData());
                $data['declarationsAndUndertakings']['declarationConfirmation'] = $confirmed;
                $form->setData($data);
            }
        }

        return $this->render('undertakings', $form);
    }

    /**
     * Get undertakings form
     *
     * @return \Zend\Form\Form
     */
    abstract protected function getForm();

    protected function formatDataForSave($data)
    {
        return $data['declarationsAndUndertakings'];
    }

    abstract protected function formatDataForForm($applicationData);

    abstract protected function updateForm($form, $data);

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
