<?php

/**
 * External Licence Authorisation Section
 *
 * External - Licence Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * External Licence Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ExternalLicenceAuthorisationSection
{
    /**
     * Holds the traffic area bundle
     *
     * @var array
     */
    protected $reviewTrafficAreaBundle = array(
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
    protected $operatingCentreAddressBundle = array(
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
     * Review-only options - we set the traffic area field in a different way because of the method scope.
     *
     * @param \Zend\Form\Form $form
     * @param array $fieldsetMap
     * @param object $context
     * @param array $options
     * @return \Zend\Form\Form
     */
    protected static function alterFormForReview($form, $fieldsetMap, $context, $options)
    {
        $form->get($fieldsetMap['dataTrafficArea'])->remove('trafficArea');

        $application = $context->makeRestCall(
            'Application',
            'GET',
            $options['data']['id'],
            $this->reviewTrafficAreaBundle
        );

        $value = isset($application['licence']['trafficArea'])
            ? $application['licence']['trafficArea']['name']
            : 'unset';

        $form->get($fieldsetMap['dataTrafficArea'])->get('trafficAreaInfoNameExists')->setValue($value);

        return $form;
    }

    /**
     * Alter action form for Goods licences
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionFormForGoods($form)
    {
        $form->remove('advertisements');
        $form->get('data')->remove('sufficientParking');
        $form->get('data')->remove('permission');
    }

    /**
     * Post set form data
     *
     * @param \Zend\Form\Form $form
     * @return \Zend\Form\Form
     */
    protected function postSetFormData($form)
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
     * This method is implemented so we can re-use some other code, it doesn't do anything yet
     *
     * @param \Zend\Form\Form $form
     */
    protected function alterActionForm($form)
    {
        $form = $this->doAlterActionForm($form);

        $this->alterActionFormForLicence($form);

        $filter = $form->getInputFilter();

        $this->disableElements($form->get('address'));
        $this->disableValidation($filter->get('address'));

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
        $data = $this->makeRestCall('LicenceOperatingCentre', 'GET', $id, $this->operatingCentreAddressBundle);

        return $data['operatingCentre']['address'];
    }
}
