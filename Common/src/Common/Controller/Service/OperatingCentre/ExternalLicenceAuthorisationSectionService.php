<?php

/**
 * External Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;

/**
 * External Licence Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalLicenceAuthorisationSectionService extends AbstractLicenceAuthorisationSectionService
{
    /**
     * Holds the traffic area bundle
     *
     * @var array
     */
    private $reviewTrafficAreaBundle = array(
        'children' => array(
            'licence' => array(
                'children' => array(
                    'trafficArea' => array(
                        'properties' => array(
                            'name'
                        )
                    )
                )
            )
        )
    );

    /**
     * Operating centre address bundle
     *
     * @var array
     */
    private $operatingCentreAddressBundle = array(
        'properties' => array(),
        'children' => array(
            'operatingCentre' => array(
                'properties' => array(),
                'children' => array(
                    'address' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'addressLine1',
                            'addressLine2',
                            'addressLine3',
                            'addressLine4',
                            'postcode',
                            'town'
                        ),
                        'children' => array(
                            'countryCode' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Alter action form
     *
     * @param \Zend\Form\Form $form
     */
    public function alterActionForm(Form $form)
    {
        $form = parent::alterActionForm($form);

        $addressElement = $form->get('address');

        $this->disableElements($addressElement);
        $this->disableValidation($form->getInputFilter()->get('address'));

        $lockedElements = array(
            $addressElement->get('searchPostcode'),
            $addressElement->get('addressLine1'),
            $addressElement->get('town'),
            $addressElement->get('postcode'),
            $addressElement->get('countryCode'),
        );

        foreach ($lockedElements as $element) {
            $this->lockElement($element, 'operating-centre-address-requires-variation');
        }

        return $form;
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param \Zend\Form\Form $form
     * @param array $options
     *
     * @return $form
     */
    public function makeFormAlterations(Form $form, $options = array())
    {
        $form = parent::makeFormAlterations($form, $options);

        $fieldsetMap = $this->getFieldsetMap($form, $options);

        if ($options['isReview']) {
            $form->get($fieldsetMap['dataTrafficArea'])->remove('trafficArea');

            $application = $this->makeRestCall(
                'Application',
                'GET',
                $options['data']['id'],
                $this->reviewTrafficAreaBundle
            );

            $value = isset($application['licence']['trafficArea'])
                ? $application['licence']['trafficArea']['name']
                : 'unset';

            $form->get($fieldsetMap['dataTrafficArea'])->get('trafficAreaInfoNameExists')->setValue($value);
        }

        return $form;
    }

    /**
     * Post set form data
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    public function postSetFormData(Form $form)
    {
        if ($this->isAction()) {
            $postData = (array)$this->getRequest()->getPost();

            $addressData = $this->getOperatingCentreAddressData($this->getActionId());

            $postData['address'] = $addressData;
            $postData['address']['countryCode'] = $postData['address']['countryCode']['id'];

            $form->setData($postData);
        }

        return $form;
    }

    /**
     * Get operating centre address data from a licence_operating_centre id
     *
     * @param int $id
     * @return array
     */
    protected function getOperatingCentreAddressData($id)
    {
        $data = $this->makeRestCall($this->getActionService(), 'GET', $id, $this->operatingCentreAddressBundle);

        return $data['operatingCentre']['address'];
    }

    /**
     * Remove the advertisements fieldset and the confirmation checkboxes
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods(Form $form)
    {
        $form->remove('advertisements')
            ->get('data')
            ->remove('sufficientParking')
            ->remove('permission');
    }
}
