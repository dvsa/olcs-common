<?php

/**
 * AbstractTrailersController.php
 */

namespace Common\Controller\Lva;

use Zend\Form\FormInterface;

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
    /**
     * Trait to support a CRUD table.
     */
    use Traits\CrudTableTrait;

    /*
     * The key for the guidance text displayed below the form.
     */
    const GUIDANCE_LABEL = 'licence_goods-trailers_trailer.table.guidance';

    /**
     * The section identifier.
     *
     * @var string $section
     */
    protected $section = 'trailors';

    public function indexAction()
    {
        $form = $this->getServiceLocator()
                     ->get('Helper\Form')
                     ->createForm('Lva\Trailers');

        $this->alterForm($form);

        $this->getServiceLocator()->get('Script')->loadFile('lva-crud');

        return $this->render('trailer', $form);
    }

    public function addAction()
    {

    }

    public function editAction()
    {

    }

    public function deleteAction()
    {

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
     */
    protected function alterForm(FormInterface $form)
    {
        $translator = $this->getServiceLocator()->get('translator');

        $form->get('table')
            ->get('table')
            ->setTable($this->getTable());

        $form->get('guidance')
            ->get('guidance')
            ->setValue($translator->translate(
                self::GUIDANCE_LABEL
            ));
    }
}