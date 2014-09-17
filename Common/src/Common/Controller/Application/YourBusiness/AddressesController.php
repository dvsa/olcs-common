<?php

/**
 * Addresses Controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
namespace Common\Controller\Application\YourBusiness;

/**
 * Addresses Controller
 *
 * @author Jakub Igla <jakub.igla@valtech.co.uk>
 */
class AddressesController extends YourBusinessController
{
    const MAIN_CONTACT_DETAILS_TYPE = 'ct_corr';

    /**
     * Set the service for the "Free" save behaviour
     *
     * @var string
     */
    protected $service = 'Application';

    /**
     * Holds the data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'properties' => array(),
                'children' => array(
                    'organisation' => array(
                        'properties' => array(),
                        'children' => array(
                            'contactDetails' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'emailAddress'
                                ),
                                'children' => array(
                                    'address' => array(
                                        'properties' => array(
                                            'id',
                                            'version',
                                            'addressLine1',
                                            'addressLine2',
                                            'addressLine3',
                                            'addressLine4',
                                            'postcode',
                                            'town'
                                        ),
                                        'children' => array(
                                            'countryCode' => array(
                                                'properties' => array(
                                                    'id'
                                                )
                                            )
                                        )
                                    ),
                                    'contactType' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                        )
                    ),
                    'contactDetails' => array(
                        'properties' => array(
                            'id',
                            'version',
                            'emailAddress'
                        ),
                        'children' => array(
                            'phoneContacts' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'phoneNumber'
                                ),
                                'children' => array(
                                    'phoneContactType' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                            'address' => array(
                                'properties' => array(
                                    'id',
                                    'version',
                                    'addressLine1',
                                    'addressLine2',
                                    'addressLine3',
                                    'addressLine4',
                                    'postcode',
                                    'town'
                                ),
                                'children' => array(
                                    'countryCode' => array(
                                        'properties' => array(
                                            'id'
                                        )
                                    )
                                )
                            ),
                            'contactType' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );


    public static $organisationTypeBundle = array(
        'properties' => array(),
        'children' => array(
            'licence' => array(
                'children' => array(
                    'licenceType' => array(
                        'properties' => array(
                            'id'
                        )
                    ),
                    'organisation' => array(
                        'children' => array(
                            'type' => array(
                                'properties' => array(
                                    'id'
                                )
                            )
                        )
                    )
                )
            )
        )
    );

    /**
     * Holds the data map
     *
     * @var mixed
     */
    protected $dataMap = null;

    /**
     * Phone types
     *
     * @var array
     */
    protected $phoneTypes = array(
        'business' => 'phone_t_tel',
        'home' => 'phone_t_home',
        'mobile' => 'phone_t_mobile',
        'fax' => 'phone_t_fax'
    );

    /**
     * Type map
     *
     * @var array
     */
    protected $typeMap = array(
        self::MAIN_CONTACT_DETAILS_TYPE => 'correspondence',
        'ct_est' => 'establishment',
        'ct_reg' => 'registered_office',
        'phone_t_tel' => 'phone_business',
        'phone_t_home' => 'phone_home',
        'phone_t_mobile' => 'phone_mobile',
        'phone_t_fax' => 'phone_fax'
    );

    /**
     * Render the section form
     *
     * @return Response
     */
    public function indexAction()
    {
        $view = $this->getViewModel();

        return $this->renderSection($view);
    }

    /**
     * Make form alterations
     *
     * This method enables the summary to apply the same form alterations. In this
     * case we ensure we manipulate the form based on whether the license is PSV or not
     *
     * @param Form $form
     * @param mixed $context
     * @param array $options
     *
     * @return $form
     */
    public static function makeFormAlterations($form, $context, $options = array())
    {
        $allowedLicTypes = array(
            self::LICENCE_TYPE_STANDARD_NATIONAL,
            self::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        $allowedOrgTypes = array(
            self::ORG_TYPE_REGISTERED_COMPANY,
            self::ORG_TYPE_LLP
        );

        $data = $context->makeRestCall(
            'Application',
            'GET',
            array('id' => $context->getIdentifier()),
            self::$organisationTypeBundle
        );

        // Need to enumerate the form fieldsets with their mapping, as we're
        // going to use old/new
        $fieldsetMap = array();
        if ($options['isReview']) {
            foreach ($options['fieldsets'] as $fieldset) {
                $fieldsetMap[$form->get($fieldset)->getAttribute('unmappedName')] = $fieldset;
            }
        } else {
            $fieldsetMap = array(
                'establishment' => 'establishment',
                'establishment_address' => 'establishment_address',
                'registered_office' => 'registered_office',
                'registered_office_address' => 'registered_office_address'
            );
        }

        if (!in_array($data['licence']['licenceType']['id'], $allowedLicTypes)) {
            $form->remove($fieldsetMap['establishment']);
            $form->remove($fieldsetMap['establishment_address']);
        }

        if (!in_array($data['licence']['organisation']['type']['id'], $allowedOrgTypes)) {
            $form->remove($fieldsetMap['registered_office']);
            $form->remove($fieldsetMap['registered_office_address']);
        }

        if ( $options['isReview'] ) {
            // Hide the search boxes
        }

        return $form;
    }

    /**
     * Alter form
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $options=array(
            'isReview' => false
        );
        $form=$this->makeFormAlterations($form, $this, $options);

        return $form;
    }

    /**
     * Get application data
     *
     * @return array
     */
    protected function getApplicationData()
    {
        return $this->makeRestCall('Application', 'GET', array('id' => $this->getIdentifier()), self::$addressBundle);
    }

    /**
     * Save data
     *
     * @param array $data
     * @param string $service
     * @return array
     */
    protected function save($data, $service = null)
    {
        $licenceId = $this->getLicenceId();

        $correspondence = array(
            'id' => $data['correspondence']['id'],
            'version' => $data['correspondence']['version'],
            'contactType' => self::MAIN_CONTACT_DETAILS_TYPE,
            'licence' => $licenceId,
            'emailAddress' => $data['contact']['email'],
            'addresses' => array(
                'address' => $data['correspondence_address'],
            )
        );

        // persist correspondence details
        $correspondenceDetails = parent::save($correspondence, 'ContactDetails');

        $correspondenceId = isset($correspondenceDetails['id'])
            ? $correspondenceDetails['id']
            : $data['correspondence']['id'];

        foreach ($this->phoneTypes as $phoneType => $phoneRefName) {

            $phone = array(
                'id' => $data['contact']['phone_' . $phoneType . '_id'],
                'version' => $data['contact']['phone_' . $phoneType . '_version'],
            );

            if (!empty($data['contact']['phone_' . $phoneType])) {

                $phone['phoneNumber'] = $data['contact']['phone_' . $phoneType];
                $phone['phoneContactType'] = $phoneRefName;
                $phone['contactDetails'] = $correspondenceId;

                parent::save($phone, 'PhoneContact');

            } elseif ((int)$phone['id'] > 0) {
                $this->makeRestCall('PhoneContact', 'DELETE', $phone);
            }
        }

        if (!empty($data['establishment'])) {

            $establishment = array(
                'id' => $data['establishment']['id'],
                'version' => $data['establishment']['version'],
                'contactType' => 'ct_est',
                'licence' => $licenceId,
                'addresses' => array(
                    'address' => $data['establishment_address'],
                )
            );

            parent::save($establishment, 'ContactDetails');
        }

        if (!empty($data['registered_office'])) {

            $organisation = $this->getOrganisationData(['id']);

            $registeredOffice = array(
                'id' => $data['registered_office']['id'],
                'version' => $data['registered_office']['version'],
                'contactType' => 'ct_reg',
                'organisation' => $organisation['id'],
                'addresses' => array(
                    'address' => $data['registered_office_address'],
                )
            );

            parent::save($registeredOffice, 'ContactDetails');
        }

        return $correspondenceDetails;
    }

    /**
     * Process load data for the form
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        $app = $data;
        $data = array(
            'contact' => array(
                'phone-validator' => true
            )
        );

        $contactDetailsMerge = array_merge(
            $app['licence']['contactDetails'],
            $app['licence']['organisation']['contactDetails']
        );

        foreach ($contactDetailsMerge as $contactDetails) {

            if (!isset($contactDetails['contactType']['id'])) {
                continue;
            }

            // Convert the DB type to the form type
            $dbType = $contactDetails['contactType']['id'];
            $type = $this->mapFormTypeFromDbType($dbType);

            $data[$type] = array(
                'id' => $contactDetails['id'],
                'version' => $contactDetails['version'],
            );

            $data[$type . '_address'] = $contactDetails['address'];
            $data[$type . '_address']['countryCode'] = $contactDetails['address']['countryCode']['id'];

            if ($dbType == self::MAIN_CONTACT_DETAILS_TYPE) {

                $data['contact']['email'] = $contactDetails['emailAddress'];

                foreach ($contactDetails['phoneContacts'] as $phoneContact) {

                    $phoneType = $this->mapFormTypeFromDbType($phoneContact['phoneContactType']['id']);

                    $data['contact'][$phoneType] = $phoneContact['phoneNumber'];
                    $data['contact'][$phoneType . '_id'] = $phoneContact['id'];
                    $data['contact'][$phoneType . '_version'] = $phoneContact['version'];
                }
            }
        }

        return $data;
    }

    /**
     * Map form type from db type
     *
     * @param string $type
     */
    protected function mapFormTypeFromDbType($type)
    {
        return (isset($this->typeMap[$type]) ? $this->typeMap[$type] : '');
    }
}
