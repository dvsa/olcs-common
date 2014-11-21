<?php

/**
 * Shared logic between Convictions Penalties controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva;

/**
 * Shared logic between Convictions Penalties controllers
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractConvictionsPenaltiesController extends AbstractController
{
    use Traits\CrudTableTrait;

    protected $section = 'convictions_penalties';

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = $this->getFormData();
        }

        $form = $this->getConvictionsPenaltiesForm()->setData($data);

        $this->alterFormForLva($form);

        if ($request->isPost()) {

            $crudAction = $this->getCrudAction(array($data['data']['table']));

            if ($crudAction !== null) {
                $this->getServiceLocator()->get('Helper\Form')->disableEmptyValidation($form);
            }

            if ($form->isValid()) {

                $this->save($data);
                $this->postSave('convictions_penalties');

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('convictions_penalties');
            }
        }

        $this->getServiceLocator()->get('Script')->loadFiles(['lva-crud', 'convictions-penalties']);

        return $this->render('convictions_penalties', $form);
    }

    protected function save($data)
    {
        $saveData = array_merge($data['data'], $data['convictionsConfirmation']);
        $saveData['id'] = $this->getApplicationId();
        $saveData['prevConviction'] = $saveData['question'];
        unset($saveData['question']);

        $this->getServiceLocator()->get('Entity\Application')->save($saveData);
    }

    protected function getFormData()
    {
        $data = $this->getServiceLocator()->get('Entity\Application')
            ->getConvictionsPenaltiesData($this->getApplicationId());

        return array(
            'data' => array(
                'version' => $data['version'],
                'question' => $data['prevConviction']
            ),
            'convictionsConfirmation' => array(
                'convictionsConfirmation' => $data['convictionsConfirmation']
            )
        );
    }

    protected function getConvictionsPenaltiesForm()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        $form = $formHelper->createForm('Lva\ConvictionsPenalties');

        $formHelper->populateFormTable(
            $form->get('data')->get('table'),
            $this->getConvictionsPenaltiesTable(),
            'data[table]'
        );

        return $form;
    }

    protected function getConvictionsPenaltiesTable()
    {
        return $this->getServiceLocator()->get('Table')
            ->prepareTable('lva-convictions-penalties', $this->getTableData());
    }

    protected function getTableData()
    {
        return $this->getServiceLocator()->get('Entity\PreviousConviction')
            ->getDataForApplication($this->getApplicationId());
    }

    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    protected function addOrEdit($mode)
    {
        $request = $this->getRequest();
        $data = array();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($mode === 'edit') {
            $id = $this->params('child_id');
            $data = $this->getConvictionData($id);
        }

        $form = $this->getPreviousConvictionForm()->setData($data);

        if ($mode === 'edit') {
            $form->get('form-actions')->remove('addAnother');
        }

        if ($request->isPost() && $form->isValid()) {

            $this->savePreviousConviction($mode, $form->getData());

            return $this->handlePostSave();
        }

        return $this->render($mode . '_convictions_penalties', $form);
    }

    protected function delete()
    {
        $ids = explode(',', $this->params('child_id'));

        $service = $this->getServiceLocator()->get('Entity\PreviousConviction');

        foreach ($ids as $id) {
            $service->delete($id);
        }
    }

    protected function getPreviousConvictionForm()
    {
        return $this->getServiceLocator()->get('Helper\Form')->createForm('Lva\PreviousConviction');
    }

    protected function getConvictionData($id)
    {
        return array(
            'data' => $this->getServiceLocator()->get('Entity\PreviousConviction')->getData($id)
        );
    }

    protected function savePreviousConviction($mode, $data)
    {
        $saveData = $data['data'];

        $saveData['application'] = $this->getApplicationId();

        if ($mode === 'edit') {
            $saveData['id'] = $this->params('child_id');
        }

        $this->getServiceLocator()->get('Entity\PreviousConviction')->save($saveData);
    }
}
