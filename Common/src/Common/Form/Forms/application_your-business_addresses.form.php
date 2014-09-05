<?php

return array(
    'application_your-business_addresses' => array(
        'name' => 'application_your-business_addresses',
        'fieldsets' => array(
            array(
                'name' => 'correspondence',
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    )
                )
            ),
            array(
                'name' => 'correspondence_address',
                'type' => 'address',
                'options' => array(
                    'label' => 'application_your-business_business-type.correspondence.label'
                ),
            ),
            array(
                'name' => 'contact',
                'options' => array(
                    'label' => 'application_your-business_business-type.contact-details.label',
                    'hint' => 'application_your-business_business-type.contact-details.hint'
                ),
                'elements' => array(
                    'phone-validator' => array(
                        'type' => 'hiddenPhoneValidation'
                    ),
                    'phone_business' => array(
                        'type' => 'phone',
                        'label' => 'application_your-business_business-type.contact-details.business-phone'
                    ),
                    'phone_business_id' => array(
                        'type' => 'hidden'
                    ),
                    'phone_business_version' => array(
                        'type' => 'hidden'
                    ),
                    'phone_home' => array(
                        'type' => 'phone',
                        'label' => 'application_your-business_business-type.contact-details.home-phone'
                    ),
                    'phone_home_id' => array(
                        'type' => 'hidden'
                    ),
                    'phone_home_version' => array(
                        'type' => 'hidden'
                    ),
                    'phone_mobile' => array(
                        'type' => 'phone',
                        'label' => 'application_your-business_business-type.contact-details.mobile-phone'
                    ),
                    'phone_mobile_id' => array(
                        'type' => 'hidden'
                    ),
                    'phone_mobile_version' => array(
                        'type' => 'hidden',
                    ),
                    'phone_fax' => array(
                        'type' => 'phone',
                        'label' => 'application_your-business_business-type.contact-details.fax-phone'
                    ),
                    'phone_fax_id' => array(
                        'type' => 'hidden'
                    ),
                    'phone_fax_version' => array(
                        'type' => 'hidden'
                    ),
                    'email' => array(
                        'type' => 'email',
                        'label' => 'application_your-business_business-type.contact-details.email'
                    )
                )
            ),
            array(
                'name' => 'establishment',
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    )
                )
            ),
            array(
                'name' => 'establishment_address',
                'type' => 'address',
                'options' => array(
                    'label' => 'application_your-business_business-type.establishment.label'
                )
            ),
            array(
                'name' => 'registered_office',
                'elements' => array(
                    'id' => array(
                        'type' => 'hidden'
                    ),
                    'version' => array(
                        'type' => 'hidden'
                    )
                )
            ),
            array(
                'name' => 'registered_office_address',
                'type' => 'address',
                'options' => array(
                    'label' => 'application_your-business_business-type.registered-office.label'
                )
            ),
            array(
                'type' => 'journey-buttons'
            )
        )
    )
);
