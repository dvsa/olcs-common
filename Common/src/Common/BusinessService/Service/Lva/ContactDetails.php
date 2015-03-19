<?php

/**
 * Contact Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;

/**
 * Contact Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContactDetails implements BusinessServiceInterface
{
    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];

        $responseData = [];

        $saved = $this->getServiceLocator()->get('Entity\ContactDetails')->save($data);

        if (isset($data['id'])) {
            $responseData['id'] = $data['id'];
        } else {
            $responseData['id'] = $saved['id'];
        }

        $response = new Response();
        $response->setType(Response::TYPE_PERSIST_SUCCESS);
        $response->setData($responseData);

        return $response;
    }
}
