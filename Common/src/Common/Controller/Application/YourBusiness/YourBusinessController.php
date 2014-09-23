<?php

/**
 * YourBusiness Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

use Common\Controller\Application\Application\ApplicationController;

/**
 * YourBusiness Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 */
class YourBusinessController extends ApplicationController
{
    /**
     * Set the service for the "Free" save behaviour
     *
     * @var string
     */
    protected $service = 'Organisation';

    /**
     * Redirect to the first section
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->goToFirstSubSection();
    }

    /**
     * Get organisation data
     *
     * @param array $organisationBundle
     * @return array
     */
    protected function getOrganisationData($extraBundle = array())
    {
        if ( isset($extraBundle['properties']) ) {
            $extraBundle['properties'] = array_unique(
                array_merge(
                    array('id', 'version'),
                    $extraBundle['properties']
                )
            );
        } else {
            $extraBundle['properties'] = array('id','version');
        }

        if ( !isset($extraBundle['children']) ) {
            $extraBundle['children'] = array();
        }

        // It matters to our testing in what order the bundle is specified
        // so it can match the relevant REST call, so reorder them here.
        $organisationBundle=array();
        if ( ! empty($extraBundle['properties']) ) {
            $organisationBundle['properties']=$extraBundle['properties'];
        }

        if ( ! empty($extraBundle['children']) ) {
            $organisationBundle['children']=$extraBundle['children'];
        }

        $bundle = array(
            'children' => array(
                'licence' => array(
                    'children' => array(
                        'organisation' => $organisationBundle
                    )
                )
            )
        );

        $application = $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), $bundle);

        return $application['licence']['organisation'];
    }
}
