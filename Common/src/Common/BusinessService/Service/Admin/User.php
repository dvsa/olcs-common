<?php

/**
 * User Business Service
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\BusinessService\Service\Admin;

use Common\BusinessService\BusinessServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Common\BusinessService\BusinessServiceAwareInterface;
use Common\BusinessService\BusinessServiceAwareTrait;
use Common\BusinessService\Response;
use Common\Exception\ResourceNotFoundException;
use Common\BusinessRule\BusinessRuleAwareInterface;
use Common\BusinessRule\BusinessRuleAwareTrait;

/**
 * User Business Service
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class User implements
    BusinessServiceInterface,
    ServiceLocatorAwareInterface,
    BusinessServiceAwareInterface,
    BusinessRuleAwareInterface
{
    use ServiceLocatorAwareTrait,
        BusinessRuleAwareTrait,
        BusinessServiceAwareTrait;

    /**
     * Processes the data by passing it through a number of business rules and then persisting it
     *
     * @param array $params
     * @return Response
     * @throws ResourceNotFoundException
     */
    public function process(array $params)
    {
        $existingData = [];
        $userDataService = $this->getServiceLocator()->get('DataServiceManager')->get('Common\Service\Data\User');
        // Get existing user where neccessary to use their id and version values
        if (isset($params['id']) && !empty($params['id'])) {
            $userId = $params['id'];
            $existingData = $userDataService->getAllUserDetails($userId);

            //check user exists exists
            if (!isset($existingData['id'])) {
                $response = new Response();
                $response->setType(Response::TYPE_FAILED);
                $response->setData(['error' => 'User not found']);
                return $response;
            }
        }

        $dataToSave = array();
        // set up user table data
        if (isset($existingData['id']) && isset($existingData['version'])) {
            $dataToSave['id'] = $existingData['id'];
            $dataToSave['version'] = $existingData['version'];
        }
        $dataToSave['loginId'] = $params['userLoginSecurity']['loginId'];
        $dataToSave['memorableWord'] = $params['userLoginSecurity']['memorableWord'];
        $dataToSave['mustResetPassword'] = $params['userLoginSecurity']['mustResetPassword'];
        $dataToSave['accountDisabled'] = $params['userLoginSecurity']['accountDisabled'];

        $dataToSave['lockedDate'] = $this->getBusinessRuleManager()
            ->get('LockedDate')
            ->validate($dataToSave['accountDisabled']);

        $dataToSave['team'] = isset($params['userType']['team']) ? $params['userType']['team'] : null;

        foreach ($params['userType']['roles'] as $role) {
            $dataToSave['userRoles'][] = [
                'role' => $role,
            ];
        }

        // set up contact data
        if (isset($existingData['contactDetails']['id']) && isset($existingData['contactDetails']['version'])) {
            $dataToSave['contactDetails']['id'] = $existingData['contactDetails']['id'];
            $dataToSave['contactDetails']['version'] = $existingData['contactDetails']['version'];
        }
        $dataToSave['contactDetails']['address'] = $params['address'];
        $dataToSave['contactDetails']['emailAddress'] = $params['userContactDetails']['emailAddress'];
        $dataToSave['contactDetails']['contactType'] = 'ct_team_user';

        // set up person
        if (isset($existingData['contactDetails']['person']['id']) &&
            isset($existingData['contactDetails']['person']['version'])) {
            $dataToSave['contactDetails']['person']['id'] = $existingData['contactDetails']['person']['id'];
            $dataToSave['contactDetails']['person']['version'] = $existingData['contactDetails']['person']['version'];
        }
        $dataToSave['contactDetails']['person']['forename'] = $params['userPersonal']['forename'];
        $dataToSave['contactDetails']['person']['familyName'] = $params['userPersonal']['familyName'];

        if (isset($params['userPersonal']['birthDate'])) {
            if (is_string($params['userPersonal']['birthDate'])) {
                // convert back to array
                $dateArray = explode('-', $params['userPersonal']['birthDate']);
                if (count($dateArray) == 3) {
                    $date = [
                        'day' => $dateArray[2],
                        'month' => $dateArray[1],
                        'year' => $dateArray[0]
                    ];
                } else {
                    return null;
                }
            }
            $dataToSave['contactDetails']['person']['birthDate'] = $this->getBusinessRuleManager()
                ->get('BirthDate')
                ->validate(
                    $date
                );
        }

        // set up phoneContacts
        if (isset($existingData['contactDetails']['phoneContacts'])) {
            $dataToSave['contactDetails']['phoneContacts'] = $this->getBusinessRuleManager()
                ->get('PhoneContacts')
                ->validate(
                    $existingData['contactDetails']['phoneContacts'],
                    $params['userContactDetails']['phone'],
                    $params['userContactDetails']['fax']
                );
        } else {
            $dataToSave['contactDetails']['phoneContacts'] = $this->getBusinessRuleManager()
                ->get('PhoneContacts')
                ->validate(
                    [],
                    $params['userContactDetails']['phone'],
                    $params['userContactDetails']['fax']
                );
        }

        $dataToSave['transportManager'] = $params['userType']['transportManager'];
        $dataToSave['localAuthority'] = $params['userType']['localAuthority'];

        // set up cascading entities
        $dataToSave['_OPTIONS_'] = array(
            'cascade' => array(
                'single' => array(
                    'contactDetails' => array(
                        'entity' => 'ContactDetails'
                    )
                ),
                'list' => array(
                    'userRoles' => array(
                        'entity' => 'UserRole',
                        'parent' => 'user'
                    )
                )
            )
        );

        if (!empty($dataToSave['contactDetails']['phoneContacts'])) {
            $dataToSave['contactDetails']['_OPTIONS_'] = array(
                'cascade' => array(
                    'list' => array(
                        'phoneContacts' => array(
                            'entity' => 'PhoneContact',
                            'parent' => 'contactDetails'
                        )
                    )
                )
            );
        }

        $result = $userDataService->saveUserRole($dataToSave);
        $response = new Response();

        if (isset($result) && !empty($result)) {
            $response->setType(Response::TYPE_SUCCESS);
            $response->setData(['id' => $result]);
        } else {
            $response->setType(Response::TYPE_FAILED);
            $response->setData(['error' => 'Unable to save']);
        }

        return $response;
    }

    /**
     * Uses existing data to determine the user type
     *
     * @param $existingData
     * @return string
     */
    public function determineUserType($existingData)
    {
        if (isset($existingData['team'])) {
            return 'internal';
        } elseif (isset($existingData['localAuthority'])) {
            return 'local-authority';
        } elseif (isset($existingData['transportManager'])) {
            return 'transport-manager';
        } elseif (isset($existingData['partnerContactDetails'])) {
            return 'partner';
        } else {
            return 'self-service';
        }
    }
}
