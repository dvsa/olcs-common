<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

use Zend\Form\Form;

/**
 * Common (aka Internal) Variation People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleAdapter extends AbstractPeopleAdapter
{
    protected function getTableConfig($orgId)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return 'lva-people';
        }

        return 'lva-variation-people';
    }

    /**
     * Extend the abstract behaviour to get the table data for the main form
     *
     * @return array
     */
    protected function getTableData($orgId)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return parent::getTableData($orgId);
        }

        $appId = $this->getApplicationAdapter()->getIdentifier();

        $data = $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->getTableData($orgId, $appId);

        return $this->tableData = $this->formatTableData($data);
    }

    public function delete($orgId, $id)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return parent::delete($orgId, $id);
        }

        $appId = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->deletePerson($orgId, $id, $appId);
    }

    public function restore($orgId, $id)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return parent::restore($orgId, $id);
        }

        $appId = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->restorePerson($orgId, $id, $appId);
    }

    public function save($orgId, $data)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return parent::save($orgId, $data);
        }

        $appId = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->savePerson($orgId, $data, $appId);
    }

    public function getPersonPosition($orgId, $personId)
    {
        if ($this->doesNotRequireDeltas($orgId)) {
            return parent::getPersonPosition($orgId, $personId);
        }

        $appId = $this->getApplicationAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->getPersonPosition($orgId, $appId, $personId);
    }

    protected function doesNotRequireDeltas($orgId)
    {
        return $this->isExceptionalOrganisation($orgId);
    }
}
