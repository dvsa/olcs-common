<?php

/**
 * Application Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Application Lva Adapter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationLvaAdapter extends AbstractLvaAdapter
{
    public function getIdentifier()
    {
        $id = $this->getController()->params('application');

        if ($id === null) {
            throw new \Exception('Can\'t get the application id from this controller');
        }

        return $id;
    }
}
