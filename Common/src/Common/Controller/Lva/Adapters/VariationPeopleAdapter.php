<?php

/**
 * Common (aka Internal) Application People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Common\Controller\Lva\Adapters;

/**
 * Common (aka Internal) Variation People Adapter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class VariationPeopleAdapter extends AbstractPeopleAdapter
{
    protected function getTableConfig()
    {
        if (!$this->useDeltas()) {
            return 'lva-people';
        }
        return 'lva-variation-people';
    }

    /**
     * Get the backend command to create a Person
     *
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getCreateCommand($params)
    {
        $params['id'] = $this->getApplicationId();
        return \Dvsa\Olcs\Transfer\Command\Application\CreatePeople::create($params);
    }

    /**
     * Get the backend command to update a Person
     *
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getUpdateCommand($params)
    {
        $params['person'] = $params['id'];
        $params['id'] = $this->getApplicationId();
        return \Dvsa\Olcs\Transfer\Command\Application\UpdatePeople::create($params);
    }

    /**
     * Get the backend command to delete a Person
     *
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getDeleteCommand($params)
    {
        $params['id'] = $this->getApplicationId();
        return \Dvsa\Olcs\Transfer\Command\Application\DeletePeople::create($params);
    }
}
