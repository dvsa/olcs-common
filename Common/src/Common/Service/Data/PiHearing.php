<?php

/**
 * Pi Hearing service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

/**
 * Pi Hearing service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PiHearing extends AbstractData
{
    protected $serviceName = 'PiHearing';

    /**
     * @param array $params
     * @return mixed
     */
    public function fetchList($params)
    {
        $params['bundle'] = json_encode(empty($bundle) ? $this->getBundle() : $bundle);
        return $this->getRestClient()->get($params);
    }

    /**
     * @return array
     */
    public function getBundle()
    {
        return [
            'children' => [
                'piVenue' => []
            ]
        ];
    }
}
