<?php

/**
 * Abstract Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\AbstractLvaFormService;
use Zend\Form\Form;

/**
 * Abstract Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractOperatingCentres extends AbstractLvaFormService
{
    protected $mainTableConfigName = 'lva-operating-centres';

    public function getForm($params)
    {
        $form = $this->getFormHelper()->createForm('Lva\OperatingCentres');

        $additionalParams = isset($params['query']) ? $params['query'] : [];
        $table = $this->getFormServiceLocator()->getServiceLocator()->get('Table')
            ->prepareTable($this->mainTableConfigName, $params['operatingCentres'], $additionalParams);

        $this->getFormHelper()->populateFormTable($form->get('table'), $table);

        $this->alterForm($form, $params);

        return $form;
    }

    protected function alterForm(Form $form, array $params)
    {
        if (!$params['canHaveSchedule41']) {
            $form->get('table')->get('table')->getTable()->removeAction('schedule41');
        }

        if (!$params['canHaveCommunityLicences']) {
            $this->getFormHelper()->remove($form, 'data->totCommunityLicences');
        }

        if ($params['isPsv']) {
            $this->alterFormForPsvLicences($form, $params);
            $this->alterFormTableForPsv($form);
        } else {
            $this->alterFormForGoodsLicences($form);
        }

        // modify the table validation message
        $this->getFormHelper()
            ->getValidator($form, 'table->table', 'Common\Form\Elements\Validators\TableRequiredValidator')
            ->setMessage('OperatingCentreNoOfOperatingCentres.required', 'required');

        if ($this->removeTrafficAreaElements($params)) {
            $this->getFormHelper()->remove($form, 'dataTrafficArea');

            return $form;
        }

        if (isset($params['licence'])) {
            $trafficArea = $params['licence']['trafficArea'];
        } else {
            $trafficArea = $params['trafficArea'];
        }

        $trafficAreaId = $trafficArea ? $trafficArea['id'] : null;

        $dataTrafficAreaFieldset = $form->get('dataTrafficArea');

        // if application/licence is NI then don't show trafficArea help
        if ($params['niFlag'] === 'Y') {
            $this->getFormHelper()->remove($form, 'dataTrafficArea->trafficAreaHelp');
        }

        if (empty($trafficAreaId) || $this->allowChangingTrafficArea($trafficAreaId)) {
            $dataTrafficAreaFieldset->get('trafficArea')->setValueOptions($params['possibleTrafficAreas']);
            $dataTrafficAreaFieldset->remove('trafficAreaSet');
        } else {
            $this->getFormHelper()->remove($form, 'dataTrafficArea->trafficArea');
            $dataTrafficAreaFieldset->get('trafficAreaSet')->setValue($trafficArea['name']);
        }

        $dataTrafficAreaFieldset->get('enforcementArea')
            ->setValueOptions($params['possibleEnforcementAreas']);

        return $form;
    }

    /**
     * Can the traffic aread be changed
     *
     * @return boolean
     */
    protected function allowChangingTrafficArea($trafficAreaId)
    {
        return false;
    }

    /**
     * Should the Traffic Area elements be removed from the Form
     *
     * @param array $data
     *
     * @return bool
     */
    protected function removeTrafficAreaElements($data)
    {
        return empty($data['operatingCentres']);
    }

    protected function alterFormForPsvLicences(Form $form, array $params)
    {
        $dataOptions = $form->get('data')->getOptions();
        $dataOptions['hint'] .= '.psv';
        $form->get('data')->setOptions($dataOptions);

        $removeFields = [
            'totAuthTrailers'
        ];

        $this->getFormHelper()->removeFieldList($form, 'data', $removeFields);
    }

    protected function alterFormTableForPsv(Form $form)
    {
        $table = $form->get('table')->get('table')->getTable();

        $table->removeColumn('noOfTrailersRequired');

        $footer = $table->getFooter();
        if (isset($footer['total']['content'])) {
            $footer['total']['content'] .= '-psv';
            unset($footer['trailersCol']);
            $table->setFooter($footer);
        }
    }

    protected function alterFormForGoodsLicences(Form $form)
    {
    }
}
