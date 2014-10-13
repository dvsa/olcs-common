<?php

/**
 * LicenceHistory Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Controller\Application\PreviousHistory;

use Common\Controller\Traits;

/**
 * LicenceHistory Controller
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceHistoryController extends PreviousHistoryController
{
    use Traits\GenericIndexAction;

    /**
     * Set the service for the "Free" save behaviour
     *
     * @var string
     */
    protected $service = 'Application';

    /**
     * Data bundle
     *
     * @var array
     */
    protected $dataBundle = array(
        'properties' => array(
            'id',
            'version',
            'prevHasLicence',
            'prevHadLicence',
            'prevBeenRefused',
            'prevBeenRevoked',
            'prevBeenDisqualifiedTc',
            'prevBeenAtPi',
            'prevPurchasedAssets'
        )
    );

    /**
     * Data map
     *
     * @var array
     */
    protected $dataMap = array(
        'main' => array(
            'mapFrom' => array(
                'dataLicencesCurrent',
                'dataLicencesApplied',
                'dataLicencesRevoked',
                'dataLicencesRefused',
                'dataLicencesDisqualified',
                'dataLicencesPublicInquiry',
                'dataLicencesHeld'
            ),
        )
    );

    /**
     * Set the action service
     *
     * @var string
     */
    protected $actionService = 'PreviousLicence';

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    protected $actionDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'licNo',
            'holderName',
            'willSurrender',
            'purchaseDate',
            'disqualificationDate',
            'disqualificationLength'
        ),
        'children' => array(
            'previousLicenceType' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Holds the actionDataBundle
     *
     * @var array
     */
    public static $tableDataBundle = array(
        'properties' => array(
            'id',
            'version',
            'licNo',
            'holderName',
            'willSurrender',
            'purchaseDate',
            'disqualificationDate',
            'disqualificationLength'
        ),
        'children' => array(
            'previousLicenceType' => array(
                'properties' => array('id')
            )
        )
    );

    /**
     * Licence type - current
     */
    const PREV_LICENCE_TYPE_HAS_LICENCE = 'prev_has_licence';

    /**
     * Licence type - applied
     */
    const PREV_LICENCE_TYPE_HAD_LICENCE = 'prev_had_licence';

    /**
     * Licence type - refused
     */
    const PREV_LICENCE_TYPE_BEEN_REFUSED = 'prev_been_refused';

    /**
     * Licence type - revoked
     */
    const PREV_LICENCE_TYPE_BEEN_REVOKED = 'prev_been_revoked';

    /**
     * Licence type - public inquiry
     */
    const PREV_LICENCE_TYPE_BEEN_AT_PI = 'prev_been_at_pi';

    /**
     * Licence type - disqualified
     */
    const PREV_LICENCE_TYPE_BEEN_DISQUALIFIED = 'prev_been_disqualified_tc';

    /**
     * Licence type - held
     */
    const PREV_LICENCE_TYPE_HAS_PURCHASED_ASSETS = 'prev_has_purchased_assets';

    /**
     * Form tables name
     *
     * @var string
     */
    protected $formTables = array(
        'table-licences-current' => 'previous_licences_current',
        'table-licences-applied' => 'previous_licences_applied',
        'table-licences-refused' => 'previous_licences_refused',
        'table-licences-revoked' => 'previous_licences_revoked',
        'table-licences-public-inquiry' => 'previous_licences_public_inquiry',
        'table-licences-disqualified' => 'previous_licences_disqualified',
        'table-licences-held' => 'previous_licences_held'
    );

    /**
     * Map a table to its type. This also maps review table types.
     *
     * @var array
     */
    public static $mapTableToType = array(
        'table-licences-current' => self::PREV_LICENCE_TYPE_HAS_LICENCE,
        'table-licences-applied' => self::PREV_LICENCE_TYPE_HAD_LICENCE,
        'table-licences-refused' => self::PREV_LICENCE_TYPE_BEEN_REFUSED,
        'table-licences-revoked' => self::PREV_LICENCE_TYPE_BEEN_REVOKED,
        'table-licences-public-inquiry' => self::PREV_LICENCE_TYPE_BEEN_AT_PI,
        'table-licences-disqualified' => self::PREV_LICENCE_TYPE_BEEN_DISQUALIFIED,
        'table-licences-held' => self::PREV_LICENCE_TYPE_HAS_PURCHASED_ASSETS,

        'previous_licences_current' => self::PREV_LICENCE_TYPE_HAS_LICENCE,
        'previous_licences_applied' => self::PREV_LICENCE_TYPE_HAD_LICENCE,
        'previous_licences_refused' => self::PREV_LICENCE_TYPE_BEEN_REFUSED,
        'previous_licences_revoked' => self::PREV_LICENCE_TYPE_BEEN_REVOKED,
        'previous_licences_public_inquiry' => self::PREV_LICENCE_TYPE_BEEN_AT_PI,
        'previous_licences_disqualified' => self::PREV_LICENCE_TYPE_BEEN_DISQUALIFIED,
        'previous_licences_held' => self::PREV_LICENCE_TYPE_HAS_PURCHASED_ASSETS
    );

    /**
     * Save method
     *
     * @param array $data
     * @param string $service
     */
    protected function save($data, $service = null)
    {
        $data['id'] = $this->getIdentifier();
        parent::save($data, $service);
    }

    /**
     * Get the form table data
     *
     * @param int $id
     * @param string $table
     * @return array
     */
    protected function getFormTableData($id, $table)
    {
        $tableData = $this->getSummaryTableData($id, $this, $table);

        return $tableData;
    }

    /**
     * Retrieve the relevant table data as we want to render it on the review summary page
     * Note that as with most controllers this is the same data we want to render on the
     * normal form page, hence why getFormTableData (declared earlier) simply wraps this
     */
    public static function getSummaryTableData($id, $context, $table)
    {
        $previousLicenceType = isset(self::$mapTableToType[$table]) ? self::$mapTableToType[$table] : null;

        $data = $context->makeRestCall(
            'PreviousLicence',
            'GET',
            array('application' => $id, 'previousLicenceType' => $previousLicenceType),
            self::$tableDataBundle
        );

        return $data;
    }

    /**
     * Add custom validation logic
     *
     * @param Form $form
     * @return Form
     */
    protected function alterForm($form)
    {
        $post = (array)$this->getRequest()->getPost();

        $tables = [
            'table-licences-current', 'table-licences-applied', 'table-licences-refused',
            'table-licences-revoked', 'table-licences-public-inquiry', 'table-licences-disqualified',
            'table-licences-held'
        ];
        $fieldsets = [
            'dataLicencesCurrent', 'dataLicencesApplied', 'dataLicencesRefused',
            'dataLicencesRevoked', 'dataLicencesPublicInquiry', 'dataLicencesDisqualified',
            'dataLicencesHeld'
        ];
        $fields = [
            'prevHasLicence', 'prevHadLicence', 'prevBeenRefused',
            'prevBeenRevoked', 'prevBeenAtPi', 'prevBeenDisqualifiedTc',
            'prevPurchasedAssets'
        ];
        $shouldAddValidators = true;
        for ($i = 0; $i < count($tables); $i++) {
            if (array_key_exists($tables[$i], $post) &&
                  array_key_exists('action', $post[$tables[$i]])) {
                $shouldAddValidators = false;
                break;
            }
        }
        if ($shouldAddValidators) {
            for ($i = 0; $i < count($tables); $i++) {
                $rows = $form->get($tables[$i])->get('rows')->getValue();
                $licenceValidator =
                    new \Common\Form\Elements\Validators\PreviousHistoryLicenceHistoryLicenceValidator();
                $licenceValidator->setRows($rows);
                $prevHasLicence = $form->getInputFilter()->get($fieldsets[$i])->get($fields[$i])->getValidatorChain();
                $prevHasLicence->attach($licenceValidator);
            }
        }
        return $form;
    }

    /**
     * Action save
     *
     * @param array $data
     * @param string $service
     */
    protected function actionSave($data, $service = null)
    {
        $applicationId = $this->getIdentifier();
        $data['application'] = $applicationId;
        if (array_key_exists('willSurrender', $data) !== false) {
            $data['willSurrender'] = ($data['willSurrender'] == 'Y') ? 1 : 0;
        }
        parent::actionSave($data, $service);

    }

    /**
     * Process action load
     *
     * @param $data
     */
    protected function processActionLoad($data)
    {
        $data = parent::processActionLoad($data);

        $actionName = $this->getActionName();

        $parts = explode('-', $actionName);

        $action = array_pop($parts);
        $type = implode('-', $parts);

        if ($action == 'add' && isset(self::$mapTableToType[$type])) {
            $data['previousLicenceType'] = self::$mapTableToType[$type];
        }

        if (array_key_exists('willSurrender', $data)) {
            if ($data['willSurrender'] === true) {
                $data['willSurrender'] = 'Y';
            } elseif ($data['willSurrender'] === false) {
                $data['willSurrender'] = 'N';
            }
        }

        if ($action != 'add') {

            $data['previousLicenceType'] = $data['previousLicenceType']['id'];
        }

        return array(
            'data' => $data
        );
    }

    protected function alterActionForm($form)
    {
        switch ($this->getActionName()) {
            case 'table-licences-current-add':
            case 'table-licences-current-edit':
                $form->get('data')->remove('disqualificationDate');
                $form->get('data')->remove('disqualificationLength');
                $form->get('data')->remove('purchaseDate');
                $form->getInputFilter()->get('data')->remove('disqualificationDate');
                $form->getInputFilter()->get('data')->remove('purchaseDate');
                break;
            case 'table-licences-applied-add':
            case 'table-licences-applied-edit':
            case 'table-licences-refused-add':
            case 'table-licences-refused-edit':
            case 'table-licences-revoked-add':
            case 'table-licences-revoked-edit':
            case 'table-licences-public-inquiry-add':
            case 'table-licences-public-inquiry-edit':
                $form->get('data')->remove('willSurrender');
                $form->get('data')->remove('disqualificationDate');
                $form->get('data')->remove('disqualificationLength');
                $form->get('data')->remove('purchaseDate');
                $form->getInputFilter()->get('data')->remove('disqualificationDate');
                $form->getInputFilter()->get('data')->remove('purchaseDate');
                break;
            case 'table-licences-disqualified-add':
            case 'table-licences-disqualified-edit':
                $form->get('data')->remove('willSurrender');
                $form->get('data')->remove('purchaseDate');
                $form->getInputFilter()->get('data')->remove('purchaseDate');
                break;
            case 'table-licences-held-add':
            case 'table-licences-held-edit':
                $form->get('data')->remove('willSurrender');
                $form->get('data')->remove('disqualificationDate');
                $form->get('data')->remove('disqualificationLength');
                $form->getInputFilter()->get('data')->remove('disqualificationDate');
                break;
            default:
                break;
        }

        return $form;
    }

    /**
     * Process load
     *
     * @param array $data
     * @return array
     */
    protected function processLoad($data)
    {
        $returnData = array(
            'id' => $data['id'],
        );
        $fieldsets = ['dataLicencesCurrent', 'dataLicencesApplied', 'dataLicencesRevoked', 'dataLicencesRefused',
            'dataLicencesPublicInquiry', 'dataLicencesDisqualified', 'dataLicencesHeld'];
        $fields = ['prevHasLicence', 'prevHadLicence', 'prevBeenRevoked', 'prevBeenRefused',
            'prevBeenAtPi', 'prevBeenDisqualifiedTc', 'prevPurchasedAssets'];

        for ($i = 0; $i < count($fieldsets); $i++) {
            if ($data[$fields[$i]] == 'Y') {
                $returnData[$fieldsets[$i]][$fields[$i]] = 'Y';
            } elseif ($data[$fields[$i]] == 'N') {
                $returnData[$fieldsets[$i]][$fields[$i]] = 'N';
            } else {
                $returnData[$fieldsets[$i]][$fields[$i]] = '';
            }
        }

        $returnData['dataLicencesCurrent']['version'] = $data['version'];
        return $returnData;
    }

    /**
     * Add current licence
     */
    public function tableLicencesCurrentAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit current licence
     */
    public function tableLicencesCurrentEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete current licence
     */
    public function tableLicencesCurrentDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add applied licence
     */
    public function tableLicencesAppliedAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit applied licence
     */
    public function tableLicencesAppliedEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete applied licence
     */
    public function tableLicencesAppliedDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add refused licence
     */
    public function tableLicencesRefusedAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit refused licence
     */
    public function tableLicencesRefusedEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete refused licence
     */
    public function tableLicencesRefusedDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add revoked licence
     */
    public function tableLicencesRevokedAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit revoked licence
     */
    public function tableLicencesRevokedEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete revoked licence
     */
    public function tableLicencesRevokedDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add public inquiry licence
     */
    public function tableLicencesPublicInquiryAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit public inquiry licence
     */
    public function tableLicencesPublicInquiryEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete public inquiry licence
     */
    public function tableLicencesPublicInquiryDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add disqualified licence
     */
    public function tableLicencesDisqualifiedAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit disqualified licence
     */
    public function tableLicencesDisqualifiedEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete disqualified licence
     */
    public function tableLicencesDisqualifiedDeleteAction()
    {
        return $this->delete();
    }

    /**
     * Add held licence
     */
    public function tableLicencesHeldAddAction()
    {
        return $this->renderSection();
    }

    /**
     * Edit held licence
     */
    public function tableLicencesHeldEditAction()
    {
        return $this->renderSection();
    }

    /**
     * Delete held licence
     */
    public function tableLicencesHeldDeleteAction()
    {
        return $this->delete();
    }
}
