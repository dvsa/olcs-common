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
     * @return array
     */
    protected function getBundle()
    {
        return [
            'children' => [
                'piVenue' => []
            ]
        ];
    }
}
