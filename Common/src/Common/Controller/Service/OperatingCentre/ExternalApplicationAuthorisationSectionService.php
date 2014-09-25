<?php

/**
 * External Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Service\OperatingCentre;

use Zend\Form\Form;

/**
 * External Application Authorisation Section Service
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExternalApplicationAuthorisationSectionService extends AbstractApplicationAuthorisationSectionService
{
    /**
     * Holds the traffic area bundle
     *
     * @var array
     */
    private $trafficAreaBundle = array(
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

            $application = $this->makeRestCall('Application', 'GET', $options['data']['id'], $this->trafficAreaBundle);

            $value = isset($application['licence']['trafficArea'])
                ? $application['licence']['trafficArea']['name']
                : 'unset';

            $form->get($fieldsetMap['dataTrafficArea'])->get('trafficAreaInfoNameExists')->setValue($value);
        }

        return $form;
    }
}
