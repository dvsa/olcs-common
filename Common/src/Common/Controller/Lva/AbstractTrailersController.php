<?php

/**
 * Abstract Trailers Controller
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Common\Controller\Lva;

use Dvsa\Olcs\Transfer\Command\Licence\UpdateTrailers;
use Zend\Form\FormInterface;
use Zend\Stdlib\RequestInterface;
use Dvsa\Olcs\Transfer\Query\Trailer\Trailer;
use Dvsa\Olcs\Transfer\Query\Licence\Trailers;
use Dvsa\Olcs\Transfer\Command\Trailer\CreateTrailer;
use Dvsa\Olcs\Transfer\Command\Trailer\UpdateTrailer;
use Dvsa\Olcs\Transfer\Command\Trailer\DeleteTrailer;
use Common\Service\Table\TableBuilder as Table;

/**
 * Abstract Trailers Controller
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
abstract class AbstractTrailersController extends AbstractController
{
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

        $response = $this->handleQuery(Trailers::create(['id' => $this->getIdentifier()]));
        $result = $response->getResult();

        if ($request->isPost()) {
            $data = (array)$request->getPost();
        } else {
            $data = \Common\Data\Mapper\Lva\Trailers::mapFromResult($result);
        }

        $form = $this->getForm($request);
        $table = $this->getTable($result['trailers']);
        $this->alterForm($form, $table);

        $form->setData($data);

        if ($request->isPost() && $form->isValid()) {

            $formData = (array)$form->getData();

            $dtoData = [
                'id' => $this->getIdentifier(),
                'shareInfo' => $formData['trailers']['shareInfo']
            ];

            $response = $this->handleCommand(UpdateTrailers::create($dtoData));

            if ($response->isOk()) {
                $crudAction = $this->getCrudAction($data);

                if ($crudAction !== null) {
                    return $this->handleCrudAction($crudAction);
                }

                return $this->completeSection('trailers');

            } else {
                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentUnknownError();
            }
        }

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

                $response = $this->handleCommand(CreateTrailer::create($data));

                if ($response->isOk()) {
                    return $this->handlePostSave(null, false);
                }

                $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');
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

                $response = $this->handleCommand(UpdateTrailer::create($data));

                if ($response->isOk()) {
                    return $this->handlePostSave(null, false);
                }
            }
        }

        $query = Trailer::create(['id' => $this->params()->fromRoute('child_id', null)]);

        $response = $this->handleQuery($query);

        if (!$response->isOk()) {
            $this->getServiceLocator()->get('Helper\FlashMessenger')->addCurrentErrorMessage('unknown-error');

            return $this->notFoundAction();
        }

        $trailer = $response->getResult();

        $form->setData(['data' => $trailer]);

        return $this->render('edit_trailer', $form);
    }

    /**
     * Method is called from the deleteAction in the abstract.
     *
     * @return boolean
     */
    public function delete()
    {
        $id = $this->params('child_id');
        $ids = explode(',', $id);

        $dto = DeleteTrailer::create(['ids' => $ids]);

        $response = $this->handleCommand($dto);

        return $response->isOk();
    }

    /**
     * Prepare and return the form with the form data.
     *
     * @return Table The trailers table.
     */
    protected function getTable($tableData)
    {
        return $this->getServiceLocator()->get('Table')->prepareTable('lva-trailers', $tableData);
    }

    /**
     * Return the trailer form.
     *
     * @param RequestInterface $request The request
     *
     * @return \Zend\Form\Form $form The form
     */
    protected function getForm(RequestInterface $request)
    {
        return $this->getServiceLocator()->get('Helper\Form')->createFormWithRequest('Lva\Trailers', $request);
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

        $form->get('table')->get('table')->setTable($table);
    }
}
