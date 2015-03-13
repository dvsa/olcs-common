<?php

/**
 * AbstractTrailersController.php
 */

namespace Common\Controller\Lva;

use Zend\Form\FormInterface;
use Zend\Stdlib\RequestInterface;

/**
 * Class AbstractTrailersController
 *
 * Controller for managing (cruding) the licences trailers.
 *
 * @package Common\Controller\Lva
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
abstract class AbstractTrailersController extends AbstractController
{
    /*
     * The key for the guidance text displayed below the form.
     */
    const GUIDANCE_LABEL = 'licence_goods-trailers_trailer.table.guidance';

    /**
     * Trait to support a CRUD table.
     */
    use Traits\CrudTableTrait;

    /**
     * The section identifier.
     *
     * @var string $section
     */
    protected $section = 'trailers';

    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
            $crudAction = $this->getCrudAction($data);

            if ($crudAction !== null) {
                return $this->handleCrudAction($crudAction);
            }

            return $this->completeSection('trailers');
        }

        $form = $this->getForm($request);
        $table = $this->getTable();

        $this->alterForm($form, $table);

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('trailer', $form);
    }

    /**
     * Delegating method for adding a new trailer.
     *
     * @return mixed
     */
    public function addAction()
    {
        return $this->addOrEdit('add');
    }

    /**
     * Delegating method for editing a trailer.
     *
     * @return mixed
     */
    public function editAction()
    {
        return $this->addOrEdit('edit');
    }

    /**
     * Method is called from the deleteAction in the abstract.
     *
     * @return void
     */
    public function delete()
    {
        $id = $this->params('child_id');

        $ids = explode(',', $id);
        foreach($ids as $id) {
            $this->getServiceLocator()->get('Entity\Trailer')->delete($id);
        }
    }

    /**
     * Delegate methods to handle adding and editing trailers.
     *
     * @param null|string $method Add or edit.
     *
     * @return mixed
     */
    protected function addOrEdit($method = null)
    {
        if (!is_string($method) || !in_array($method, ["add", "edit"])) {
            throw new \InvalidArgumentException(
                __METHOD__ . " expects argument 'add' or 'edit'."
            );
        }

        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\Trailer', $request);

        $data = array();
        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } elseif ($method === 'edit') {
            $form->get('form-actions')->remove('addAnother');
            $data = array(
                'data' => $this->getServiceLocator()
                    ->get('Entity\Trailer')
                    ->getById($this->params('child_id'))
            );
        }

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {
            $data = $form->getData()['data'];
            $data['licence'] = $this->getLicenceId();

            if ($method === 'add') {
                $data['specifiedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate();
            }

            $this->getServiceLocator()->get('Entity\Trailer')->save($data);

            return $this->handlePostSave();
        }

        return $this->render($method . '_trailer', $form);
    }

    /**
     * Prepare and return the form with the form data.
     *
     * @return Table The trailers table.
     */
    protected function getTable()
    {
        return $this->getServiceLocator()
            ->get('Table')
            ->prepareTable('lva-trailers', $this->getTableData());
    }

    /**
     * Return the trailer form.
     *
     * @param RequestInterface $request The request
     *
     * @return Form $form The form
     */
    protected function getForm(RequestInterface $request)
    {
        return $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\Trailers', $request);
    }

    /**
     * Pull trailer data from the backend and format it for display
     * within a table.
     *
     * @return array The trailer table rows.
     */
    protected function getTableData()
    {
        $data = $this->getServiceLocator()
                     ->get('Entity\Trailer')
                     ->getTrailerDataForLicence($this->getLicenceId());

        $tableData = [];
        foreach ($data['Results'] as $key => $trailer) {
            $tableData[] = [
                'id' => $trailer['id'],
                'trailerNo' => $trailer['trailerNo'],
                'specifiedDate' => $trailer['specifiedDate']
            ];
        }

        return $tableData;
    }

    /**
     * Alter the form to add the table and set the guidance.
     *
     * @param FormInterface $form The form.
     * @param Table $table The table to add to the form.
     */
    protected function alterForm(FormInterface $form, $table)
    {
        $translator = $this->getServiceLocator()->get('translator');

        $form->get('table')
            ->get('table')
            ->setTable($table);

        $form->get('guidance')
            ->get('guidance')
            ->setValue(
                $translator->translate(self::GUIDANCE_LABEL)
            );
    }
}