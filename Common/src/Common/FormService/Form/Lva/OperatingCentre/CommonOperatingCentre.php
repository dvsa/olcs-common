<?php

/**
 * Common Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentre;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Helper\FormHelperService;
use Laminas\Form\Form;
use Laminas\Http\Request;

/**
 * Common Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonOperatingCentre extends AbstractFormService
{
    /**
     * Get operating centre form
     *
     * @param array   $params  Parameters for form
     * @param Request $request HTTP Request parameters
     *
     * @return \Laminas\Form\FormInterface
     */
    public function getForm(array $params, Request $request)
    {
        $form = $this->getFormHelper()->createFormWithRequest('Lva\OperatingCentre', $request);

        if ($params['action'] !== 'add') {
            $form->get('form-actions')->remove('addAnother');
        }

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Alter operating centre form
     *
     * @param Form  $form   Form model
     * @param array $params Parameters for form
     *
     * @return void
     */
    protected function alterForm(Form $form, array $params)
    {
        if ($params['isPsv']) {
            $this->alterActionFormForPsv($form);
        }

        if (!array_key_exists('isEligibleForLgv', $params) || !$params['isEligibleForLgv']) {
            // remove fields related to LGV only
            $formHelper = $this->getFormHelper();
            $formHelper->remove($form, 'data->lgvHtml');
            $formHelper->remove($form, 'data->noOfLgvVehiclesRequired');

            // keep the existing wording
            $form->get('data')->get('noOfHgvVehiclesRequired')->setOptions(
                [
                    'label' => 'application_operating-centres_authorisation-sub-action.data.noOfVehiclesRequired',
                    'error-message' => 'Your total number of vehicles',
                ]
            );
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
            // TODO LGV - this is a temporary fix which only takes into account HGV
            // this code will be reviewed and modified by VOL-2103
            $form->get('data')->get('noOfHgvVehiclesRequired')
                ->setAttribute('data-current', $params['currentHgvVehiclesRequired']);

            if ($form->get('data')->has('noOfTrailersRequired')) {
                $form->get('data')->get('noOfTrailersRequired')
                    ->setAttribute('data-current', $params['currentTrailersRequired']);
            }
        }

        $form->get('address')->get('postcode')->setOption('shouldEscapeMessages', false);
    }

    /**
     * Alter operating centre form for PSV
     *
     * @param Form $form Form model
     *
     * @return void
     */
    protected function alterActionFormForPsv(Form $form)
    {
        $this->getFormHelper()->remove($form, 'data->hgvHtml');
        $this->getFormHelper()->remove($form, 'data->trailersHtml');
        $this->getFormHelper()->remove($form, 'data->noOfTrailersRequired');
        $this->getFormHelper()->remove($form, 'advertisements');
        $this->getFormHelper()->remove($form, 'data->guidance');

        // keep the existing wording
        $form->get('data')->get('dataHtml')->setOptions(['tokens' => ['application_operating-centres_authorisation-sub-action.data-psv']]);
        $this->getFormHelper()->alterElementLabel(
            $form->get('data')->get('permission')->get('permission'),
            '-psv',
            FormHelperService::ALTER_LABEL_APPEND
        );
    }

    /**
     * Disable and lock address fields
     *
     * @param \Laminas\Form\Form $form Form model
     *
     * @return void
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
