<?php

/**
 * AbstractTrailersController.php
 */

namespace Common\Controller\Lva;

use Zend\Form\FormInterface;
use Zend\Stdlib\RequestInterface;

use Dvsa\Olcs\Transfer\Query\Trailer\Trailer;
use Dvsa\Olcs\Transfer\Query\Trailer\Trailers;
use Dvsa\Olcs\Transfer\Command\Trailer\CreateTrailer;
use Dvsa\Olcs\Transfer\Command\Trailer\UpdateTrailer;
use Dvsa\Olcs\Transfer\Command\Trailer\DeleteTrailer;

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
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\Trailer', $request);

        if ($request->isPost()) {
            $form->setData((array)$request->getPost());

            if ($form->isValid()) {
                $data = $form->getData()['data'];
                $data['id'] = $this->getLicenceId();
                $data['licence'] = $this->getLicenceId();
                $data['specifiedDate'] = $this->getServiceLocator()->get('Helper\Date')->getDate();

                $dto = CreateTrailer::create(
                    $data
                );

                $response = $this->handleCommand($dto);

                if ($response->isOk()) {
                    return $this->handlePostSave(null, false);
                }

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }
        }

        return $this->render('add_trailer', $form);
    }

    /**
     * Delegating method for editing a trailer.
     *
     * @return mixed
     */
    public function editAction()
    {
        $request = $this->getRequest();
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createFormWithRequest('Lva\Trailer', $request);

        $this->getServiceLocator()->get('Helper\Form')->remove($form, 'form-actions->addAnother');

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData()['data'];
                $data['licence'] = $this->getLicenceId();

                $dto = UpdateTrailer::create(
                    $data
                );

                $response = $this->handleCommand($dto);

                if ($response->isOk()) {
                    return $this->handlePostSave(null, false);
                }
            }
        }

        $query = Trailer::create(
            [
                'id' => $this->params()->fromRoute('child_id', null)
            ]
        );

        $response = $this->handleQuery($query);

        if (!$response->isOk()) {
            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            return $this->notFoundAction();
        }

        $gracePeriod = $response->getResult();

        $form->setData(
            array(
                'data' => $gracePeriod
            )
        );

        return $this->render('edit_trailer', $form);
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

        $dto = DeleteTrailer::create(
            [
                'ids' => $ids
            ]
        );

        $response = $this->handleCommand($dto);

        if ($response->isOk()) {
            return true;
        }

        return false;
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
        $query = Trailers::create(
            array(
                'licence' => $this->getLicenceId()
            )
        );

        $response = $this->handleQuery($query);

        if (!$response->isOk()) {
            if ($response->isClientError() || $response->isServerError()) {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addErrorMessage('unknown-error');
            }

            return $this->notFoundAction();
        }

        $result = (array)$response->getResult();

        $tableData = [];
        foreach ($result['results'] as $trailer) {
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
        $this->getServiceLocator()->get('Helper\Form')->remove($form, 'form-actions->saveAndContinue');

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
