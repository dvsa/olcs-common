<?php

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

use Common\Controller\Lva\Interfaces\AdapterAwareInterface;

/**
 * Abstract Transport Managers Controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractTransportManagersController extends AbstractController implements AdapterAwareInterface
{
    use Traits\CrudTableTrait,
        Traits\AdapterAwareTrait;

    protected $section = 'transport_managers';

    /**
     * Transport managers section
     */
    public function indexAction()
    {
        /* @var $form \Zend\Form\Form */
        $form = $this->getAdapter()->getForm();
        $table = $this->getAdapter()->getTable();
        $table->loadData($this->getAdapter()->getTableData($this->getIdentifier()));
        $form->get('table')->get('table')->setTable($table);
        $form->get('table')->get('rows')->setValue(count($table->getRows()));

        $request = $this->getRequest();
        if ($request->isPost()) {

            $data = (array) $request->getPost();
            $form->setData($data);

            // if is it not required to have at least one TM, then remove the validator
            if (!$this->getAdapter()->mustHaveAtLeastOneTm($this->getIdentifier())) {
                $form->getInputFilter()->remove('table');
            }

            $crudAction = $this->getCrudAction(array($data['table']));
            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            if ($form->isValid()) {
                $this->postSave('transport_managers');
                return $this->completeSection('transport_managers');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('transport_managers', $form);
    }

    /**
     * Handle CrudTableTrait delete
     */
    protected function delete()
    {
        // get ids to delete
        $ids = explode(',', $this->params('child_id'));

        /* @var $service \Common\BusinessService\Service\TransportManagerApplication\Delete */
        $service = $this->getServiceLocator()
            ->get('BusinessServiceManager')
            ->get('Lva\DeleteTransportManagerApplication');
        $service->process(['ids' => $ids]);
    }

    /**
     * Gives a new translation key to use for the delete modal text.
     *
     * @return string The message translation key.
     */
    protected function getDeleteMessage()
    {
        return 'review-transport_managers_delete';
    }

    /**
     * Override the delete title.
     *
     * @return string The modal message key.
     */
    protected function getDeleteTitle()
    {
        return 'delete-tm';
    }
}
