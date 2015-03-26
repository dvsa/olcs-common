<?php

/**
 * User service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Service\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

use Common\Util\RestClient;
use Common\Exception\ResourceNotFoundException;

/**
 * User service
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class User extends Generic implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $serviceName = 'User';

    /**
     * Gets the bundle
     *
     * @return array
     */
    protected function getBundle()
    {
        return [
            'children' => [
                'team' => [],
                'contactDetails' => [
                    'children' => [
                        'address' => [
                            'children' => [
                                'countryCode'
                            ]
                        ],
                        'person' => [
                            'children' => [
                                'title' => []
                            ]
                        ],
                        'phoneContacts' => [
                            'children' => [
                                'phoneContactType'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $id
     * @param Bundle $bundle
     * @throws ResourceNotFoundException
     * @return array
     */
    public function fetchMyDetailsFormData($id, $bundle = null)
    {
        //this will have been cached
        $data = $this->fetchOne($id, $bundle);

        //check publication exists
        if (!isset($data['id'])) {
            throw new ResourceNotFoundException('User not found');
        }

        return $this->getDataMapper()->formatMyDetailsDataForForm($data);
    }

    /**
     * Saves a user form
     *
     * @param array $data
     * @return mixed
     * @throws \Common\Exception\BadRequestException
     * @throws \Common\Exception\ResourceNotFoundException
     */
    public function save($data)
    {
        if (isset($data['id'])) {
            $existingData = $this->fetchOne($data['id'], $this->getBundle());

            //check user exists exists
            if (!isset($existingData['id'])) {
                throw new ResourceNotFoundException('User not found');
            }

            $mapped = $this->getDataMapper()->saveMyDetailsFormMapper($existingData, $data);

            $this->getContactDetailsService()->save($mapped['contact']);

            //any new phone contacts need adding separately
            foreach ($mapped['newPhoneContacts'] as $newPhoneContact) {
                $this->getPhoneContactService()->save($newPhoneContact);
            }

            parent::save($mapped['user']);

            return $data['id'];
        }
    }

    /**
     * Gets the contact details service
     *
     * @return \Common\Service\Data\Generic
     */
    protected function getContactDetailsService()
    {
        return $this->getServiceLocator()->get('DataServiceManager')->get('Generic\Service\Data\ContactDetails');
    }

    /**
     * Gets the phone contact service
     *
     * @return \Common\Service\Data\Generic
     */
    protected function getPhoneContactService()
    {
        return $this->getServiceLocator()->get('DataServiceManager')->get('Generic\Service\Data\PhoneContact');
    }

    /**
     * Gets the mapper
     *
     * @return \Common\Mapper\UserMapper
     */
    protected function getDataMapper()
    {
        return $this->getServiceLocator()->get('Common\Mapper\UserMapper');
    }
}
