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
            'newPhoneContacts' => $newPhoneContacts
        ];
    }

    /**
     * Formats data for the myDetails form
     *
     * @param $data
     * @return array
     */
    public function formatMyDetailsDataForForm($data)
    {
        //set the fieldmap
        $this->setFieldMap($this->getMyDetailsFieldMap());

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
            'contactDetails||person||title' => 'userDetails',
            'contactDetails||person||otherTitle' => 'userDetails',
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
}
