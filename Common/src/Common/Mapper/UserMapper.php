<?php

/**
 * User mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Common\Mapper;

/**
 * User mapper
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class UserMapper extends GenericMapper
{

    /**
     * This is effectively contact details that are being formatted,
     * based on contents of the myDetails form
     *
     * @param $existingData
     * @param $formData
     *
     * @return array
     */
    public function saveMyDetailsFormMapper($existingData, $formData)
    {
        $phoneContacts = $existingData['contactDetails']['phoneContacts'];

        $phoneFields = [
            'phone_t_tel' => 'phone',
            'phone_t_fax' => 'fax'
        ];

        //check for pre-existing phone/fax to overwrite
        foreach ($phoneContacts as $key => $phoneContact) {
            switch ($phoneContact['phoneContactType']['id']) {
                case 'phone_t_tel':
                    $phoneContacts[$key]['phoneNumber'] = $formData['userContact']['phone'];
                    unset($phoneFields[$phoneContact['phoneContactType']['id']]);
                    break;
                case 'phone_t_fax':
                    //fax isn't mandatory so can be removed
                    if ($formData['userContact']['fax'] == '') {
                        unset($phoneContacts[$key]);
                    } else {
                        $phoneContacts[$key]['phoneNumber'] = $formData['userContact']['fax'];
                    }

                    unset($phoneFields[$phoneContact['phoneContactType']['id']]);
                    break;
            }
        }

        $newPhoneContacts = [];

        //we've checked pre-existing entries, now check for any new ones
        foreach ($phoneFields as $key => $field) {
            $newPhoneContacts[] = [
                'contactDetails' => $existingData['contactDetails']['id'],
                'phoneNumber' => $formData['userContact'][$field],
                'phoneContactType' => $key
            ];
        }

        $user = [
            'id' => $existingData['id'],
            'version' => $existingData['version'],
            'team' => $formData['userDetails']['team'],
            'loginId' => $formData['userDetails']['loginId']
        ];

        //contact details - userContact fieldset
        $contact['id'] = $existingData['contactDetails']['id'];
        $contact['version'] = $existingData['contactDetails']['version'];
        $contact['emailAddress'] = $formData['userContact']['emailAddress'];
        $contact['phoneContact'] = $phoneContacts;

        //address - officeAddress fieldset
        $address = $formData['officeAddress'];
        $address['id'] = $existingData['contactDetails']['address']['id'];
        $address['version'] = $existingData['contactDetails']['address']['version'];

        //person - userDetails fieldset
        $person = $formData['userDetails'];
        $person['id'] = $existingData['contactDetails']['person']['id'];
        $person['version'] = $existingData['contactDetails']['person']['version'];

        $contact['address'] = $address;
        $contact['person'] = $person;

        //contact phone
        $contact['_OPTIONS_'] = array(
            'cascade' => array(
                'list' => array(
                    'phoneContact' => array(
                        'entity' => 'PhoneContact',
                        'parent' => 'contactDetails'
                    )
                ),
            )
        );

        return [
            'contact' => $contact,
            'newPhoneContacts' => $newPhoneContacts,
            'user' => $user
        ];
    }

    /**
     * Formats data for the myDetails form
     *
     * @param $data
     * @return array
     */
    public function formatMyDetailsDataForForm($data, $fieldMap)
    {
        //set the fieldmap
        $this->setFieldMap($fieldMap);

        //format most of the data using the generic function
        $formData = parent::formatDataForForm($data);

        //add in the confirm email address
        if (isset($data['contactDetails']['emailAddress'])) {
            $formData['userContact']['emailConfirm'] = $data['contactDetails']['emailAddress'];
        }

        //phone contacts information is taken from a list (we only want phone and fax)
        if (isset($data['contactDetails']['phoneContacts'])) {
            foreach ($data['contactDetails']['phoneContacts'] as $contact) {
                switch ($contact['phoneContactType']['id']) {
                    case 'phone_t_tel':
                        $formData['userContact']['phone'] = $contact['phoneNumber'];
                        break;
                    case 'phone_t_fax':
                        $formData['userContact']['fax'] = $contact['phoneNumber'];
                        break;
                }
            }
        }

        return $formData;
    }

    /**
     * Gets the myDetails form field map (field => fieldset)
     *
     * @return array
     */
    public function getMyDetailsFieldMap()
    {
        return [
            'team||id' => 'userDetails',
            'loginId' => 'userDetails',
            'contactDetails||person||title||id' => 'userDetails',
            'contactDetails||person||forename' => 'userDetails',
            'contactDetails||person||familyName' => 'userDetails',
            'contactDetails||person||birthDate' => 'userDetails',
            'contactDetails||emailAddress' => 'userContact',
            'contactDetails||address||addressLine1' => 'officeAddress',
            'contactDetails||address||addressLine2' => 'officeAddress',
            'contactDetails||address||addressLine3' => 'officeAddress',
            'contactDetails||address||addressLine4' => 'officeAddress',
            'contactDetails||address||town' => 'officeAddress',
            'contactDetails||address||postcode' => 'officeAddress',
            'contactDetails||address||countryCode||id' => 'officeAddress'
        ];
    }

    /**
     * Format data on form submission
     *
     * @param array $data
     * @param array $existingData
     * @return array
     */
    public function formatSave(array $data, $existingData = array())
    {
        $dataToSave = array();
        // set up user table data
        if (isset($existingData['id']) && isset($existingData['version'])) {
            $dataToSave['id'] = $existingData['id'];
            $dataToSave['version'] = $existingData['version'];
        }
        $dataToSave['loginId'] = $data['userLoginSecurity']['loginId'];
        $dataToSave['memorableWord'] = $data['userLoginSecurity']['memorableWord'];
        $dataToSave['hintQuestion1'] = isset($data['userLoginSecurity']['hintQuestion1']) ?
            $data['userLoginSecurity']['hintQuestion1'] : null;
        $dataToSave['hintAnswer1'] = isset($data['userLoginSecurity']['hintAnswer1']) ?
            $data['userLoginSecurity']['hintAnswer1'] : null;
        $dataToSave['hintQuestion2'] = isset($data['userLoginSecurity']['hintQuestion2']) ?
            $data['userLoginSecurity']['hintQuestion2'] : null;
        $dataToSave['hintAnswer2'] = isset($data['userLoginSecurity']['hintAnswer2']) ?
            $data['userLoginSecurity']['hintAnswer2'] : null;
        $dataToSave['mustResetPassword'] = $data['userLoginSecurity']['mustResetPassword'];
        $dataToSave['accountDisabled'] = $data['userLoginSecurity']['accountDisabled'];
        if ($dataToSave['accountDisabled'] == 'Y') {
            $dataToSave['lockedDate'] = date('Y-m-d H:i:s');
        } else {
            $dataToSave['lockedDate'] = null;
        }
        $dataToSave['team'] = $data['userType']['team'];

        foreach ($data['userType']['roles'] as $role) {
            $dataToSave['userRoles'][] = [
                'role' => $role,
            ];
        }

        // set up contact data
        if (isset($existingData['contactDetails']['id']) && isset($existingData['contactDetails']['version'])) {
            $dataToSave['contactDetails']['id'] = $existingData['contactDetails']['id'];
            $dataToSave['contactDetails']['version'] = $existingData['contactDetails']['version'];
        }
        $dataToSave['contactDetails']['address'] = $data['address'];
        $dataToSave['contactDetails']['emailAddress'] = $data['userContactDetails']['emailAddress'];
        $dataToSave['contactDetails']['contactType'] = 'ct_team_user';

        // set up person
        if (isset($existingData['contactDetails']['person']['id']) &&
            isset($existingData['contactDetails']['person']['version'])) {
            $dataToSave['contactDetails']['person']['id'] = $existingData['contactDetails']['person']['id'];
            $dataToSave['contactDetails']['person']['version'] = $existingData['contactDetails']['person']['version'];
        }
        $dataToSave['contactDetails']['person']['forename'] = $data['userPersonal']['forename'];
        $dataToSave['contactDetails']['person']['familyName'] = $data['userPersonal']['familyName'];
        $dataToSave['contactDetails']['person']['birthDate'] = $data['userPersonal']['birthDate'];

        // set up phoneContacts
        if (isset($existingData['contactDetails']['phoneContacts'])) {
            foreach ($existingData['contactDetails']['phoneContacts'] as $phoneContact) {
                if ($phoneContact['phoneContactType'] == 'phone_t_tel') {
                    $phoneContact['phoneNumber'] = $data['userContactDetails']['phone'];
                } elseif ($phoneContact['phoneContactType'] == 'phone_t_fax') {
                    $phoneContact['phoneNumber'] = $data['userContactDetails']['fax'];
                }
            }
            $dataToSave['contactDetails']['phoneContacts'] = $existingData['contactDetails']['phoneContacts'];
        } else {
            $phoneContact = [];
            $phoneContact['phoneNumber'] = $data['userContactDetails']['phone'];
            $phoneContact['phoneContactType'] = 'phone_t_tel';

            $faxContact['phoneNumber'] = $data['userContactDetails']['fax'];
            $faxContact['phoneContactType'] = 'phone_t_fax';

            $dataToSave['contactDetails']['phoneContacts'][] = $phoneContact;
            $dataToSave['contactDetails']['phoneContacts'][] = $faxContact;
        }

        $dataToSave['transportManager'] = $data['userType']['transportManager'];

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

        return $dataToSave;
    }

    /**
     * Format data on form submission
     *
     * @param array $data
     * @param array $existingData
     * @return array
     */
    public function formatLoad(array $existingData)
    {
        $formData = array();

        $formData['id'] = $existingData['id'];
        $formData['version'] = $existingData['version'];
        $formData['userLoginSecurity']['loginId'] = $existingData['loginId'];
        $formData['userLoginSecurity']['memorableWord'] = $existingData['memorableWord'];
        $formData['userLoginSecurity']['hintQuestion1'] = $existingData['hintQuestion1'];
        $formData['userLoginSecurity']['hintAnswer1'] = $existingData['hintAnswer1'];
        $formData['userLoginSecurity']['hintQuestion2'] = $existingData['hintQuestion2'];
        $formData['userLoginSecurity']['hintAnswer2'] = $existingData['hintAnswer2'];
        $formData['userLoginSecurity']['mustResetPassword'] = $existingData['mustResetPassword'];
        $formData['userLoginSecurity']['accountDisabled'] = $existingData['accountDisabled'];
        $formData['userLoginSecurity']['lockedDate'] = $existingData['lockedDate'];
        $formData['userType']['userType'] = $this->determineUserType($existingData);
        $formData['userType']['team'] = $existingData['team'];

        if (isset($existingData['transportManager']['id'])) {
            $formData['userType']['transportManager'] = $existingData['transportManager']['id'];
        }

        $formData['userType']['roles'] = [];

        if (isset($existingData['userRoles'])) {
            foreach ($existingData['userRoles'] as $userRole) {
                $formData['userType']['roles'][] = $userRole['role']['id'];
            }
        }

        // set up contact data
        $formData['userPersonal']['forename'] = $existingData['contactDetails']['person']['forename'];
        $formData['userPersonal']['familyName'] = $existingData['contactDetails']['person']['familyName'];
        $formData['userPersonal']['birthDate'] = $existingData['contactDetails']['person']['birthDate'];
        $formData['userContactDetails']['emailAddress'] = $existingData['contactDetails']['emailAddress'];
        $formData['userContactDetails']['emailConfirm'] = $existingData['contactDetails']['emailAddress'];

        if (isset($existingData['contactDetails']['phoneContacts'])) {
            foreach ($existingData['contactDetails']['phoneContacts'] as $phoneContact) {
                if ($phoneContact['phoneContactType']['id'] == 'phone_t_tel') {
                    $formData['userContactDetails']['phone'] = $phoneContact['phoneNumber'];
                } elseif ($phoneContact['phoneContactType']['id'] == 'phone_t_fax') {
                    $formData['userContactDetails']['fax'] = $phoneContact['phoneNumber'];
                }
            }
        }
        $formData['address'] = $existingData['contactDetails']['address'];

        if (isset($existingData['lastSuccessfulLoginDate'])) {
            $formData['userLoginSecurity']['lastSuccessfulLogin'] = date(
                'd/m/Y H:i:s',
                strtotime($existingData['lastSuccessfulLoginDate'])
            );
        }

        $formData['userLoginSecurity']['attempts'] = $existingData['attempts'];

        if (isset($existingData['lockedDate'])) {
            $formData['userLoginSecurity']['lockedDate'] = date(
                'd/m/Y H:i:s',
                strtotime($existingData['lockedDate'])
            );
        }

        if (isset($existingData['resetPasswordExpiryDate'])) {
            $formData['userLoginSecurity']['resetPasswordExpiryDate'] = date(
                'd/m/Y H:i:s',
                strtotime($existingData['resetPasswordExpiryDate'])
            );
        }
        return $formData;
    }

    /**
     * Uses existing data to determine the user type
     *
     * @param $existingData
     * @return string
     */
    private function determineUserType($existingData)
    {
        if (isset($existingData['localAuthority'])) {
            return 'local-authority';
        } else if (isset($existingData['transportManager'])) {
            return 'transport-manager';
        } else if (isset($existingData['partnerContactDetails'])) {
            return 'partner';
        } else {
            return 'self-service';
        }
    }
}
