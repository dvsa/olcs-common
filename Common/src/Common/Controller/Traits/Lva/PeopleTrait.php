<?php

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Traits\Lva;

use Common\Service\Entity\OrganisationEntityService;

/**
 * Shared logic between People controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
trait PeopleTrait
{
    public function indexAction()
    {
        $form = $this->getServiceLocator()->get('Helper\Form')
            ->createForm('Lva\People');

        $orgId = $this->getCurrentOrganisationId();
        $orgData = $this->getServiceLocator()
            ->get('Entity\Organisation')
            ->getBusinessDetailsData($orgId);

        $table = $this->getServiceLocator()
            ->get('Table')
            ->buildTable(
                'application_your-business_people_in_form',
                array(),
                array(),
                false
            );

        $column = $table->getColumn('name');
        $column['type'] = $this->lva;
        $table->setColumn('name', $column);

        $form->get('table')  // fieldset
            ->get('table')   // element
            ->setTable($table);

        $this->alterForm($form, $table, $orgData);

        return $this->render('people', $form);
    }

    private function alterForm($form, $table, $orgData)
    {
        $tableHeader = 'selfserve-app-subSection-your-business-people-tableHeader';
        $guidanceLabel = 'selfserve-app-subSection-your-business-people-guidance';

        // needed in here?
        $translator = $this->getServiceLocator()->get('translator');

        switch ($orgData['type']['id']) {
            case OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY:
                $tableHeader .= 'Directors';
                $guidanceLabel .= 'LC';
                break;

            case OrganisationEntityService::ORG_TYPE_LLP:
                $tableHeader .= 'Partners';
                $guidanceLabel .= 'LLP';
                break;

            case OrganisationEntityService::ORG_TYPE_PARTNERSHIP:
                $tableHeader .= 'Partners';
                $guidanceLabel .= 'P';
                break;

            case OrganisationEntityService::ORG_TYPE_OTHER:
                $tableHeader .= 'People';
                $guidanceLabel .= 'O';
                break;

            default:
                break;
        }

        $table->setVariable(
            'title',
            $translator->translate($tableHeader)
        );
        $form->get('guidance')
            ->get('guidance')
            ->setValue($translator->translate($guidanceLabel));
    }
}
