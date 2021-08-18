<?php

namespace Common\FormService\Form\Lva\OperatingCentres;

use Common\FormService\Form\Lva\AbstractLvaFormService;
use Laminas\Form\Form;
use Common\RefData;
use Common\Service\Table\TableBuilder;

/**
 * @see \CommonTest\FormService\Form\Lva\OperatingCentres\AbstractOperatingCentresTest
 */
abstract class AbstractOperatingCentres extends AbstractLvaFormService
{
    protected $mainTableConfigName = 'lva-operating-centres';

    /**
     * Get Form
     *
     * @param array $params Parameters
     *
     * @return Form
     */
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

    /**
     * Alter form
     *
     * @param Form  $form   Form
     * @param array $params Parameters
     *
     * @return Form
     */
    protected function alterForm(Form $form, array $params)
    {
        if (!$params['canHaveSchedule41']) {
            $form->get('table')->get('table')->getTable()->removeAction('schedule41');
        }

        if (!$params['canHaveCommunityLicences']) {
            $this->getFormHelper()->remove($form, 'data->totCommunityLicencesFieldset');
        }

        if ($params['isPsv']) {
            $this->alterFormForPsvLicences($form, $params);
            $this->alterFormTableForPsv($form);
        } else {
            $this->alterFormForGoodsLicences($form, $params);
        }

        // - Modify the validation message for Required on 'rows' field
        // The validator compares the data against the 'rows' field value.
        // This is the reason why we use table->rows instead of table->table
        // which it was previously.
        $this->getFormHelper()
            ->getValidator($form, 'table->rows', 'Common\Form\Elements\Validators\TableRequiredValidator')
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
            $form->get('dataTrafficArea')->get('trafficAreaSet')->setOption('hint', null);
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
     * @param int $trafficAreaId Traffic area id
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
     * @param array $data Data
     *
     * @return bool
     */
    protected function removeTrafficAreaElements($data)
    {
        return empty($data['operatingCentres']);
    }

    /**
     * Alter Form For Psv Licences
     *
     * @param Form  $form   Form
     * @param array $params Parameters
     *
     * @return void
     */
    protected function alterFormForPsvLicences(Form $form, array $params)
    {
        $dataFieldset = $form->get('data');

        if ($dataFieldset->has('totCommunityLicencesFieldset')) {
            $totCommunityLicencesFieldset = $dataFieldset->get('totCommunityLicencesFieldset');
            $totCommunityLicencesFieldset->setLabel('');
            $totCommunityLicencesElement = $totCommunityLicencesFieldset->get('totCommunityLicences');
            $totCommunityLicencesElement->setLabel($totCommunityLicencesElement->getLabel() . '.psv');
        }

        $dataOptions = $dataFieldset->getOptions();
        if (isset($dataOptions['hint'])) {
            $dataOptions['hint'] .= isset($dataOptions['hint']) ? '.psv' : '';
        }
        $dataFieldset->setOptions($dataOptions);

        $removeFields = [
            'totAuthTrailersFieldset'
        ];

        $this->getFormHelper()->removeFieldList($form, 'data', $removeFields);
        $this->disableVehicleClassifications($form);
    }

    /**
     * Alter Form Table For Psv
     *
     * @param Form $form Form
     *
     * @return void
     */
    protected function alterFormTableForPsv(Form $form)
    {
        $table = $form->get('table')->get('table')->getTable();
        assert($table instanceof TableBuilder);

        $table->removeColumn('noOfTrailersRequired');

        $footer = $table->getFooter();
        if (isset($footer['total']['content'])) {
            $footer['total']['content'] .= '-psv';
            unset($footer['trailersCol']);
            $table->setFooter($footer);
        }
    }

    /**
     * Alter Form For Goods Licences
     *
     * @param Form $form
     * @param array $params
     * @return void
     */
    protected function alterFormForGoodsLicences(Form $form, array $params): void
    {
        $licenceType = $params['licenceType']['id'] ?? null;
        if ($licenceType !== RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL) {
            $this->disableVehicleClassifications($form);
        }
    }

    /**
     * @param $form
     */
    protected function disableVehicleClassifications($form)
    {
        $this->getFormHelper()->remove($form, 'data->totAuthLgvVehiclesFieldset');
        $totAuthHgvVehiclesFieldset = $form->get('data')->get('totAuthHgvVehiclesFieldset');
        $totAuthHgvVehiclesFieldset->setLabel('application_operating-centres_authorisation.data.totAuthHgvVehiclesFieldset.vehicles-label');
        $totAuthHgvVehiclesFieldset->get('totAuthHgvVehicles')->setLabel('application_operating-centres_authorisation.data.totAuthHgvVehicles.vehicles-label');
    }
}
