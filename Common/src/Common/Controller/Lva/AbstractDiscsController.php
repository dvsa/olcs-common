<?php

/**
 * Abstract Discs Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Dvsa\Olcs\Transfer\Query\Licence\PsvDiscs;
use Zend\Form\Form;

/**
 * Abstract Discs Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractDiscsController extends AbstractController
{
    use Traits\CrudTableTrait;

    /**
     * Setup the section
     *
     * @var string
     */
    protected $section = 'discs';

    protected $formTableData;

    protected $spacesRemaining = null;

    // Command keys
    const CMD_REQUEST_DISCS = 'requested';
    const CMD_VOID_DISCS = 'voided';
    const CMD_REPLACE_DISCS = 'replaced';

    protected $commandMap = [
        self::CMD_REQUEST_DISCS => [
            'licence' => \Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs::class,
            'variation' => \Dvsa\Olcs\Transfer\Command\Variation\CreatePsvDiscs::class
        ],
        self::CMD_VOID_DISCS => [
            'licence' => \Dvsa\Olcs\Transfer\Command\Licence\VoidPsvDiscs::class,
            'variation' => \Dvsa\Olcs\Transfer\Command\Variation\VoidPsvDiscs::class
        ],
        self::CMD_REPLACE_DISCS => [
            'licence' => \Dvsa\Olcs\Transfer\Command\Licence\ReplacePsvDiscs::class,
            'variation' => \Dvsa\Olcs\Transfer\Command\Variation\ReplacePsvDiscs::class
        ]
    ];

    public function indexAction()
    {
        $request = $this->getRequest();

        $filterForm = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\DiscFilter');

        if ($request->isPost()) {

            $data = (array)$request->getPost();

            $crudAction = $this->getCrudAction([$data['table']]);

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('discs');

        } else {
            $data = $this->getFormData();
        }

        $form = $this->getDiscsForm()->setData($data);

        $this->alterFormForLva($form);

        $this->getServiceLocator()->get('Script')->loadFiles(['forms/filter']);

        if (!is_null($this->spacesRemaining) && $this->spacesRemaining < 0) {
            $this->getServiceLocator()->get('Helper\Guidance')->append('more-discs-than-authorisation');
        }
        return $this->render('discs', $form, ['filterForm' => $filterForm]);
    }

    public function addAction()
    {
        $request = $this->getRequest();

        $form = $this->getRequestForm();

        if ($request->isPost()) {
            $form->setData((array)$request->getPost());
        }

        if ($request->isPost() && $form->isValid()) {
            $response = $this->processRequestDiscs($form->getData());

            if ($response->isOk()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addSuccessMessage('psv-discs-' . self::CMD_REQUEST_DISCS . '-successfully');

                return $this->redirect()->toRoute(null, [$this->getIdentifierIndex() => $this->getIdentifier()]);
            }

            if ($response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')
                    ->addCurrentErrorMessage('unknown-error');
            } else {
                $this->mapErrors($form, $response->getResult()['messages']);
            }
        }

        return $this->render('add_discs', $form);
    }

    protected function processRequestDiscs($data)
    {
        $amount = $data['data']['additionalDiscs'];

        $dtoData = [
            $this->getIdentifierIndex() => $this->getIdentifier(),
            'amount' => $amount
        ];

        $commandClass = $this->commandMap[self::CMD_REQUEST_DISCS][$this->lva];
        $response = $this->handleCommand($commandClass::create($dtoData));

        return $response;
    }

    public function replaceAction()
    {
        return $this->commonConfirmCommand(self::CMD_REPLACE_DISCS);
    }

    public function voidAction()
    {
        return $this->commonConfirmCommand(self::CMD_VOID_DISCS);
    }

    protected function getRequestForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\PsvDiscsRequest');

        $form->get('form-actions')->remove('addAnother');

        return $form;
    }

    protected function getGenericConfirmationForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('GenericConfirmation');
    }

    protected function getFormData()
    {
        $discs = $this->getTableData();

        $data = array(
            'data' => array(
                'validDiscs' => 0,
                'pendingDiscs' => 0
            )
        );

        foreach ($discs as $disc) {
            if (!empty($disc['issuedDate'])) {
                $data['data']['validDiscs']++;
            } else {
                $data['data']['pendingDiscs']++;
            }
        }

        return $data;
    }

    protected function getDiscsForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $this->getServiceLocator()
            ->get('FormServiceManager')
            ->get('lva-' . $this->lva . '-' . $this->section)
            ->getForm();

        $formHelper->populateFormTable($form->get('table'), $this->getDiscsTable());

        return $form;
    }

    protected function getDiscsTable()
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-psv-discs', $this->getTableData());
    }

    protected function getTableData()
    {
        if ($this->formTableData === null) {

            $data = [
                'id' => $this->getLicenceId(),
                'includeCeased' => $this->params()->fromQuery('includeCeased', 0)
            ];

            $result = $this->handleQuery(PsvDiscs::create($data))->getResult();
            $data = $result['psvDiscs'];
            $this->spacesRemaining = $result['remainingSpacesPsv'];

            $this->formTableData = array();

            foreach ($data as $disc) {
                $disc['discNo'] = $this->getDiscNumberFromDisc($disc);
                $this->formTableData[] = $disc;
            }

            // sort discs so that "Pending" are at the top
            uasort(
                $this->formTableData,
                function ($disc1, $disc2) {
                    return ($disc1['discNo'] === 'Pending') ? -1 : 1;
                }
            );
        }

        return $this->formTableData;
    }

    protected function getDiscNumberFromDisc($disc)
    {
        if (isset($disc['discNo']) && !empty($disc['discNo'])) {
            return $disc['discNo'];
        }

        if (empty($disc['issuedDate']) && empty($disc['ceasedDate'])) {
            return 'Pending';
        }

        return '';
    }

    protected function mapErrors(Form $form, array $errors)
    {
        if (isset($errors['amount']['LIC-PSVDISC-1'])) {
            $form->setMessages(
                [
                    'data' => [
                        'additionalDiscs' => [
                            'additional-psv-discs-validator-too-many'
                        ]
                    ]
                ]
            );
            unset($errors['amount']);
        }

        if (!empty($errors)) {
            $fm = $this->getServiceLocator()->get('Helper\FlashMessenger');

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }
    }

    protected function commonConfirmCommand($commandKey)
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->commonSave($commandKey);
            return $this->redirect()->toRoute(null, [$this->getIdentifierIndex() => $this->getIdentifier()]);
        }

        $form = $this->getGenericConfirmationForm();

        return $this->render($commandKey . '_discs', $form);
    }

    protected function commonSave($commandKey)
    {
        $dtoData = [
            $this->getIdentifierIndex() => $this->getIdentifier(),
            'ids' => explode(',', $this->params('child_id'))
        ];

        $this->commonCommand($commandKey, $dtoData);
    }

    protected function commonCommand($commandKey, $dtoData)
    {
        $commandClass = $this->commandMap[$commandKey][$this->lva];
        $response = $this->handleCommand($commandClass::create($dtoData));

        if ($response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addSuccessMessage('psv-discs-' . $commandKey . '-successfully');
        } else {
            $this->getServiceLocator()->get('Helper\FlashMessenger')
                ->addErrorMessage('unknown-error');
        }
    }
}
