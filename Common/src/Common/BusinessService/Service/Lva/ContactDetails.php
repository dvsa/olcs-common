<?php

/**
 * Contact Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Contact Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetails implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Format and save the contact details data
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];

        $saved = $this->getServiceLocator()->get('Entity\ContactDetails')->save($data);

        if (!empty($data['id'])) {
            $id = $data['id'];
        } else {
            $id = $saved['id'];
        }

        return new Response(Response::TYPE_SUCCESS, ['id' => $id]);
    }
}
