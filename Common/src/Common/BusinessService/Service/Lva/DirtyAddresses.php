<?php

/**
 * Dirty Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\BusinessService\Service\Lva;

use Common\BusinessService\BusinessServiceInterface;
use Common\BusinessService\Response;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Dirty Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DirtyAddresses implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $fieldsets = [
        'correspondence' => ['fao'],
        'correspondence_address' => [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'addressLine4',
            'postcode',
            'town',
            'countryCode'
        ],
        'contact' => [
            'email',
            'phone_business',
            'phone_home',
            'phone_mobile',
            'phone_fax'
        ],
        'establishment_address' => [
            'addressLine1',
            'addressLine2',
            'addressLine3',
            'addressLine4',
            'postcode',
            'town',
            'countryCode'
        ]
    ];

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Common\BusinessService\ResponseInterface
     */
    public function process(array $params)
    {
        $original = $params['original'];
        $updated = $params['updated'];

        $dirtyFieldsets = 0;

        $helper = $this->getServiceLocator()->get('Helper\Data');
        foreach ($this->fieldsets as $fieldset => $keys) {
            if (!isset($original[$fieldset])) {
                // some fieldsets are conditionally removed. It's
                // safe enough to check their existance as even
                // a totally empty fieldset will still be set
                continue;
            }

            $diff = $helper->compareKeys(
                $original[$fieldset],
                $updated[$fieldset],
                $keys
            );
            if (!empty($diff)) {
                $dirtyFieldsets ++;
            }
        }

        $response = new Response();
        $response->setType(Response::TYPE_SUCCESS);
        $response->setData(
            [
                'dirtyFieldsets' => $dirtyFieldsets
            ]
        );

        return $response;
    }
}
