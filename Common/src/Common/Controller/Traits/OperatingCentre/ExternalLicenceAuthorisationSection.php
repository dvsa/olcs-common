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
    protected $trafficAreaBundle = array(
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

        $application = $context->makeRestCall('Application', 'GET', $options['data']['id'], $this->trafficAreaBundle);

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

        return $form;
    }
}
