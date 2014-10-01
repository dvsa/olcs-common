<?php

/**
 * BusinessType Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

use Common\Controller\Traits;

/**
 * BusinessType Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessTypeController extends YourBusinessController
{
    use Traits\GenericIndexAction;

    /**
     * Load data from id
     *
     * @param int $id
     */
    protected function load($id)
    {
        $organisationBundle = array(
            'children' => array(
                'type' => array(
                    'properties' => array(
                        'id'
                    )
                )
            )
        );

        $data = array('data' => $this->getOrganisationData($organisationBundle));

        if (isset($data['data']['type']['id'])) {
            $data['data']['type'] = $data['data']['type']['id'];
        }

        return $data;
    }
}
