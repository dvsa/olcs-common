<?php

/**
 * External Application Authorisation Section
 *
 * External - Application Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Traits\OperatingCentre;

/**
 * External Application Authorisation Section
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ExternalApplicationAuthorisationSection
{
    /**
     * Holds the traffic area bundle
     *
     * @var array
     */
    protected static $trafficAreaBundle = array(
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

        $application = $context->makeRestCall('Application', 'GET', $options['data']['id'], self::$trafficAreaBundle);

        $value = isset($application['licence']['trafficArea'])
            ? $application['licence']['trafficArea']['name']
            : 'unset';

        $form->get($fieldsetMap['dataTrafficArea'])->get('trafficAreaInfoNameExists')->setValue($value);

        return $form;
    }
}
