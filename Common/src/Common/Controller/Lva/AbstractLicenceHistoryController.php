<?php

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Licence History Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractLicenceHistoryController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $sections = array(
        'current' => 'prevHasLicence',
        'applied' => 'prevHadLicence',
        'refused' => 'prevBeenRefused',
        'revoked' => 'prevBeenRevoked',
        'public-inquiry' => 'prevBeenAtPi',
        'disqualified' => 'prevBeenDisqualifiedTc',
        'held' => 'prevPurchasedAssets'
    );

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->formatDataForForm($this->getFormData());
        }

        $form = $this->getLicenceHistoryForm()->setData($data);

        if ($request->isPost()) {

            $tables = array();
            foreach (array_keys($this->sections) as $section) {
                $tables[] = $data[$section]['table'];
            }

            print '<pre>';
            print_r($data);
            print_r($tables);
            exit;

            $crudAction = $this->getCrudAction($tables);

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);
                $this->postSave('licence_history');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('licence_history');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('licence-history');

        return $this->render('licence_history', $form);
    }

    public function addAction()
    {

    }

    public function editAction()
    {

    }

    protected function delete()
    {

    }

    protected function save($data)
    {
        $data = $this->formatDataForSave($data);

        $data['id'] = $this->getApplicationId();

        $this->getServiceLocator()->get('Entity\Application')->save($data);
    }

    protected function formatDataForSave($data)
    {
        $saveData = array();

        foreach ($this->sections as $reference => $actual) {
            $saveData[$actual] = $data[$reference]['question'];
        }

        $saveData['version'] = $data['current']['version'];

        return $saveData;
    }

    protected function getFormData()
    {
        return $this->getServiceLocator()->get('Entity\Application')->getLicenceHistoryData($this->getApplicationId());
    }

    protected function formatDataForForm($data)
    {
        $formData = array();

        foreach ($this->sections as $reference => $actual) {
            $formData[$reference] = array(
                'question' => $data[$actual]
            );
        }

        $formData['current']['version'] = $data['version'];

        return $formData;
    }

    protected function getLicenceHistoryForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\LicenceHistory');

        foreach (array_keys($this->sections) as $section) {
            $formHelper->populateFormTable($form->get($section)->get('table'), $this->getTable($section));
        }

        return $form;
    }

    protected function getTable($which)
    {
        return $this->getServiceLocator()->get('Table')
            ->prepareTable('lva-licence-history-' . $which, $this->getTableData($which));
    }

    protected function getTableData($which)
    {
        $stringHelper = $this->getServiceLocator()->get('Helper\String');

        $prevLicenceType = $stringHelper->camelToUnderscore($this->sections[$which]);

        return $this->getServiceLocator()->get('Entity\PreviousLicence')
            ->getForApplicationAndType($this->getApplicationId(), $prevLicenceType);
    }













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
