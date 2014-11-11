<?php

/**
 * Abstract Discs Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

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

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = (array)$request->getPost();

            $crudAction = $this->getCrudAction(array($data['table']));

            if ($crudAction !== null) {

                return $this->handleCrudAction($crudAction);
            }

        } else {
            $data = $this->getFormData();
        }

        $form = $this->getDiscsForm()->setData($data);

        $this->getServiceLocator()->get('Script')->loadFile('discs');

        return $this->render('discs', $form);
    }

    /**
     * Get form data
     *
     * @param array $data
     * @return array
     */
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

        $form = $formHelper->createForm('Lva\PsvDiscs');

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
            $data = $this->getServiceLocator()->get('Entity\Licence')
                ->getPsvDiscs($this->getLicenceId());

            $this->formTableData = array();

            foreach ($data as $disc) {
                if (!empty($disc['ceasedDate'])) {
                    continue;
                }

                $disc['discNo'] = $this->getDiscNumberFromDisc($disc);
                $this->formTableData[] = $disc;
            }
        }

        return $this->formTableData;
    }

    /**
     * Get disc number from a disc array
     *
     * @param array $disc
     * @return string
     */
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

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getRequestFormData();
        }

        $form = $this->getRequestForm()->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $this->processRequestDiscs($data);

            return $this->postSave('discs');
        }

        return $this->render('add_discs', $form);
    }

    /**
     * Request discs
     *
     * @param type $data
     */
    public function processRequestDiscs($data)
    {
        $additionalDiscCount = $data['data']['additionalDiscs'];

        $this->requestDiscs($additionalDiscCount, array('licence' => $this->getLicenceId()));

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addSuccessMessage('psv-discs-requested-successfully');
    }

    /**
     * Request multiple discs
     *
     * @param int $count
     * @param array $data
     */
    protected function requestDiscs($count, $data = array())
    {
        $this->getServiceLocator()->get('Entity\PsvDisc')->requestDiscs($count, $data);
    }

    /**
     * Process action load
     *
     * @param array $data
     * @return array
     */
    protected function getRequestFormData()
    {
        $data = $this->getServiceLocator()->get('Entity\Licence')
            ->getPsvDiscsRequestData($this->getLicenceId());

        $data['totalAuth'] = (
            $data['totAuthSmallVehicles'] + $data['totAuthMediumVehicles'] + $data['totAuthLargeVehicles']
        );

        $data['discCount'] = 0;

        foreach ($data['psvDiscs'] as $disc) {
            if (empty($disc['ceasedDate'])) {
                $data['discCount']++;
            }
        }

        unset($data['totAuthSmallVehicles']);
        unset($data['totAuthMediumVehicles']);
        unset($data['totAuthLargeVehicles']);
        unset($data['psvDiscs']);

        return array('data' => $data);
    }

    protected function getRequestForm()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\PsvDiscsRequest');

        $form->get('form-actions')->remove('addAnother');

        return $form;
    }

    public function replaceAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->replaceSave();

            return $this->redirect()->toRoute(null, array($this->getIdentifierIndex() => $this->getIdentifier()));
        }

        $form = $this->getGenericConfirmationForm();

        return $this->render('replace_discs', $form);
    }

    public function voidAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {

            $this->voidSave();

            return $this->redirect()->toRoute(
                null,
                array($this->getIdentifierIndex() => $this->getIdentifier())
            );
        }

        $form = $this->getGenericConfirmationForm();

        return $this->render('void_discs', $form);
    }

    protected function getGenericConfirmationForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('GenericConfirmation');
    }

    /**
     * Cease multiple discs
     *
     * @param array $ids
     */
    protected function ceaseDiscs($ids)
    {
        $this->getServiceLocator()->get('Entity\PsvDisc')->ceaseDiscs($ids);
    }

    /**
     * Replace the selected discs
     *
     * @param array $data
     */
    public function replaceSave()
    {
        $ids = explode(',', $this->params('child_id'));

        $this->ceaseDiscs($ids);

        $this->requestDiscs(count($ids), array('isCopy' => 'Y', 'licence' => $this->getLicenceId()));

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addSuccessMessage('psv-discs-replaced-successfully');
    }

    /**
     * Void multiple discs
     *
     * @param array $data
     */
    public function voidSave()
    {
        $ids = explode(',', $this->params('child_id'));

        $this->ceaseDiscs($ids);

        $this->getServiceLocator()->get('Helper\FlashMessenger')
            ->addSuccessMessage('psv-discs-voided-successfully');
    }
}
