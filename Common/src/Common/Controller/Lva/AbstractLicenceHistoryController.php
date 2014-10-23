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

            $crudAction = $this->getCrudAction(array());

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

    /**
     * Override the get crud action method
     *
     * @param array $formTables
     * @return array
     */
    protected function getCrudAction(array $formTables = array())
    {
        $data = (array)$this->getRequest()->getPost();

        foreach (array_keys($this->sections) as $section) {

            if (isset($data[$section]['table']['action'])) {

                $action = $data[$section]['table']['action'];
                $data[$section]['table']['routeAction'] = $section . '-' . strtolower($action);

                return $data[$section]['table'];
            }
        }

        return null;
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
            $formHelper->populateFormTable(
                $form->get($section)->get('table'),
                $this->getTable($section),
                $section . '[table]'
            );
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
     * Add current licence
     */
    public function currentAddAction()
    {
        return $this->addOrEdit('add', 'current');
    }

    /**
     * Edit current licence
     */
    public function currentEditAction()
    {
        return $this->addOrEdit('edit', 'current');
    }

    /**
     * Delete current licence
     */
    public function currentDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add applied licence
     */
    public function appliedAddAction()
    {
        return $this->addOrEdit('add', 'applied');
    }

    /**
     * Edit applied licence
     */
    public function appliedEditAction()
    {
        return $this->addOrEdit('edit', 'applied');
    }

    /**
     * Delete applied licence
     */
    public function appliedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add refused licence
     */
    public function refusedAddAction()
    {
        return $this->addOrEdit('add', 'refused');
    }

    /**
     * Edit refused licence
     */
    public function refusedEditAction()
    {
        return $this->addOrEdit('edit', 'refused');
    }

    /**
     * Delete refused licence
     */
    public function refusedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add revoked licence
     */
    public function revokedAddAction()
    {
        return $this->addOrEdit('add', 'revoked');
    }

    /**
     * Edit revoked licence
     */
    public function revokedEditAction()
    {
        return $this->addOrEdit('edit', 'revoked');
    }

    /**
     * Delete revoked licence
     */
    public function revokedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add disqualified licence
     */
    public function disqualifiedAddAction()
    {
        return $this->addOrEdit('add', 'disqualified');
    }

    /**
     * Edit disqualified licence
     */
    public function disqualifiedEditAction()
    {
        return $this->addOrEdit('edit', 'disqualified');
    }

    /**
     * Delete disqualified licence
     */
    public function disqualifiedDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add held licence
     */
    public function heldAddAction()
    {
        return $this->addOrEdit('add', 'held');
    }

    /**
     * Edit held licence
     */
    public function heldEditAction()
    {
        return $this->addOrEdit('edit', 'held');
    }

    /**
     * Delete held licence
     */
    public function heldDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Add public inquiry licence
     */
    public function publicInquiryAddAction()
    {
        return $this->addOrEdit('add', 'public-inquiry');
    }

    /**
     * Edit public inquiry licence
     */
    public function publicInquiryEditAction()
    {
        return $this->addOrEdit('edit', 'public-inquiry');
    }

    /**
     * Delete public inquiry licence
     */
    public function publicInquiryDeleteAction()
    {
        return $this->deleteAction();
    }

    /**
     * Generic functionality for adding/editing
     *
     * @param string $mode
     * @param string $which
     * @return mixed
     */
    protected function addOrEdit($mode, $which)
    {
        $form = $this->getLicenceForm();

        return $this->render($mode . '_' . $which . '_licence_history', $form);
    }

    protected function getLicenceForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\LicenceHistoryLicence');
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
}
