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
    protected $lva = 'variation';

    public function canModify($orgId)
    {
        return true;
    }

    // @TODO: all methods below are duplicated across int/ext
    // variation adapters
    // I don't think inheritance is the solution, so either wrap
    // another service or... something else

    protected function getTableConfig($orgId)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return 'lva-people';
        }

        return 'lva-variation-people';
    }

    public function attachMainScripts()
    {
        // @TODO switch based on exceptional type or not
        $this->getServiceLocator()->get('Script')->loadFile('lva-crud-delta');
    }

    /**
     * Extend the abstract behaviour to get the table data for the main form
     *
     * @return array
     */
    protected function getTableData($orgId)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return parent::getTableData($orgId);
        }

        $appId = $this->getVariationAdapter()->getIdentifier();

        $data = $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->getTableData($orgId, $appId);

        return $this->tableData = $this->formatTableData($data);
    }

    public function delete($orgId, $id)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return parent::save($orgId, $data);
        }

        $appId = $this->getLvaAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->deletePerson($orgId, $id, $appId);
    }

    public function restore($orgId, $id)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return parent::restore($orgId, $id);
        }

        $appId = $this->getLvaAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->restorePerson($orgId, $id, $appId);
    }

    public function save($orgId, $data)
    {
        if ($this->isExceptionalOrganisation($orgId)) {
            return parent::save($orgId, $data);
        }

        $appId = $this->getLvaAdapter()->getIdentifier();

        return $this->getServiceLocator()
            ->get('Lva\VariationPeople')
            ->savePerson($orgId, $id, $appId);
    }
}
