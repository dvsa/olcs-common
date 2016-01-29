<?php

/**
 * Common Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentre;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Helper\FormHelperService;
use Zend\Form\Form;
use Zend\Http\Request;

/**
 * Common Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonOperatingCentre extends AbstractFormService
{
    public function getForm(array $params, Request $request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\OperatingCentre', $request);

        if ($params['action'] !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm(Form $form, array $params)
    {
        if ($params['isPsv']) {
            $this->alterActionFormForPsv($form, $params);
        }

        // Set the postcode field as not required
        $form->getInputFilter()->get('address')->get('postcode')
            ->setRequired(false);

        if (!$params['canAddAnother'] && $form->get('form-actions')->has('addAnother')) {
            $form->get('form-actions')->remove('addAnother');
        }

        if (!$params['canUpdateAddress']) {
            $this->disableAddressFields($form);
        }

        if ($params['wouldIncreaseRequireAdditionalAdvertisement']) {

            $form->get('data')->get('noOfVehiclesRequired')
                ->setAttribute('data-current', $params['currentVehiclesRequired']);

            if ($form->get('data')->has('noOfTrailersRequired')) {
                $form->get('data')->get('noOfTrailersRequired')
                    ->setAttribute('data-current', $params['currentTrailersRequired']);
            }
        }

        $form->get('address')->get('postcode')->setOption('shouldEscapeMessages', false);
    }

    protected function alterActionFormForPsv(Form $form, array $params)
    {
        $this->getFormHelper()->remove($form, 'data->noOfTrailersRequired');
        $this->getFormHelper()->remove($form, 'advertisements');

        $this->getFormHelper()->alterElementLabel(
            $form->get('data'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
        $this->getFormHelper()->alterElementLabel(
            $form->get('data')->get('sufficientParking'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
        $this->getFormHelper()->alterElementLabel(
            $form->get('data')->get('permission'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
    }

    /**
     * Disable and lock address fields
     *
     * @param \Zend\Form\Form $form
     */
    protected function disableAddressFields($form)
    {
        $addressElement = $form->get('address');
        $addressElement->remove('searchPostcode');

        $this->getFormHelper()->disableElements($addressElement);
        $this->getFormHelper()->disableValidation($form->getInputFilter()->get('address'));

        $lockedElements = [
            $addressElement->get('addressLine1'),
            $addressElement->get('town'),
            $addressElement->get('postcode'),
            $addressElement->get('countryCode')
        ];

        foreach ($lockedElements as $element) {
            $this->getFormHelper()->lockElement($element, 'operating-centre-address-requires-variation');
        }
    }
}
