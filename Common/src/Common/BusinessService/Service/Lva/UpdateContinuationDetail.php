<?php

/**
 * UpdateContinuation
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\BusinessServiceInterface;

/**
 * UpdateContinuation
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateContinuationDetail implements ServiceLocatorAwareInterface, BusinessServiceInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Update Continuation Details
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $data = $params['data'];
        $id = $data['id'];
        $this->getServiceLocator()->get('Entity\ContinuationDetail')->forceUpdate($id, $data);

        return new Response(Response::TYPE_SUCCESS, ['id' => $id]);
    }
}
