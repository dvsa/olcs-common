<?php

/**
 * AccessCorrespondence.php
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class AccessCorrespondence
 *
 * Process and modity a correspondence record setting the accessed flag to be true.
 *
 * @package Common\BusinessService\Service\Lva
 */
class AccessCorrespondence implements BusinessServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Set the accessed flag on the correspondence record.
     *
     * @param array $params The correspondence record.
     *
     * @return Response
     */
    public function process(array $params)
    {
        if (!isset($params['id'])) {
            return new Response(Response::TYPE_FAILED);
        }

        $this->getServiceLocator()->get('Entity\CorrespondenceInbox')
            ->update(
                $params['id'],
                array(
                    'accessed' => 'Y',
                    'version' => $params['version']
                )
            );

        return new Response(Response::TYPE_SUCCESS);
    }
}
