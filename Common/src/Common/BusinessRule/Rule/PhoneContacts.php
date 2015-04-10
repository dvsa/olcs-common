<?php

/**
 * Phone Contacts business rule
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Common\BusinessRule\Rule;

use Common\BusinessRule\BusinessRuleInterface;

/**
 * PhoneContacts Rule
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class PhoneContacts implements BusinessRuleInterface
{

    /**
     * Validates the passed phone contacts array and assigns the correct number depending on contact type
     *
     * @param array $phoneContacts
     * @param null $phoneNumber
     * @param null $faxNumber
     * @return array
     */
    public function validate($phoneContacts = [], $phoneNumber = null, $faxNumber = null)
    {
        $newPhoneContacts = [];
        if (empty($phoneContacts)) {
            // add as new
            $phoneContact = [];
            $phoneContact['phoneNumber'] = $phoneNumber;
            $phoneContact['phoneContactType'] = 'phone_t_tel';

            $faxContact['phoneNumber'] = $faxNumber;
            $faxContact['phoneContactType'] = 'phone_t_fax';

            $newPhoneContacts[] = $phoneContact;
            $newPhoneContacts[] = $faxContact;
        } else {
            // update existing
            foreach ($phoneContacts as $phoneContact) {
                $newPhoneContact = $phoneContact;
                if ($phoneContact['phoneContactType'] == 'phone_t_tel' && !empty($phoneNumber)) {
                    $newPhoneContact['phoneNumber'] = $phoneNumber;
                } elseif ($phoneContact['phoneContactType'] == 'phone_t_fax') {
                    $newPhoneContact['phoneNumber'] = $faxNumber;
                }
                if (!empty($newPhoneContact['phoneNumber'])) {
                    $newPhoneContacts[] = $phoneContact;
                }
            }
        }
        return $newPhoneContacts;

    }
}
