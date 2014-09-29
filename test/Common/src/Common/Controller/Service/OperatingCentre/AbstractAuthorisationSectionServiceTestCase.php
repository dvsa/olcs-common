<?php

/**
 * Abstract Authorisation Section Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

use CommonTest\Controller\Service\AbstractSectionServiceTestCase;

/**
 * Abstract Authorisation Section Service TestCase
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractAuthorisationSectionServiceTestCase extends AbstractSectionServiceTestCase
{
    protected function getActionForm()
    {
        $formName = 'application_operating-centres_authorisation-sub-action';

        $form = $this->serviceManager->get('OlcsCustomForm')->createForm($formName);

        return $form;
    }

    protected function getAuthorisationForm()
    {
        $formName = 'application_operating-centres_authorisation';
        $tableName = 'authorisation_in_form';

        $data['url'] = $this->getMock('\stdClass', array('fromRoute'));
        $table = $this->serviceManager->get('Table')->buildTable($tableName, array(), $data, false);

        $form = $this->serviceManager->get('OlcsCustomForm')->createForm($formName);
        $form->get('table')->get('table')->setTable($table, 'table');

        $form->get('data')->setAttribute('unmappedName', 'data');
        $form->get('dataTrafficArea')->setAttribute('unmappedName', 'dataTrafficArea');
        $form->get('table')->setAttribute('unmappedName', 'table');

        return $form;
    }
}
