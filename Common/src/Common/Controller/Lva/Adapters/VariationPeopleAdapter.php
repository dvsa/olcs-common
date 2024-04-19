<?php

namespace Common\Controller\Lva\Adapters;

use Dvsa\Olcs\Transfer\Command\Licence\CreatePeople;
use Dvsa\Olcs\Transfer\Command\Application\CreatePeople as CreatePeopleApplication;
use Dvsa\Olcs\Transfer\Command\Licence\DeletePeople;
use Dvsa\Olcs\Transfer\Command\Application\DeletePeople as DeletePeopleApplication;
use Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople;
use Dvsa\Olcs\Transfer\Command\Application\UpdatePeople as UpdatePeopleApplication;
use Psr\Container\ContainerInterface;

class VariationPeopleAdapter extends AbstractPeopleAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    protected function getTableConfig(): string
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
    protected function getCreateCommand($params): CreatePeople|CreatePeopleApplication
    {
        $params['id'] = $this->getApplicationId();
        return CreatePeople::create($params);
    }

    /**
     * Get the backend command to update a Person
     *
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getUpdateCommand($params): UpdatePeople|UpdatePeopleApplication
    {
        $params['person'] = $params['id'];
        $params['id'] = $this->getApplicationId();
        return UpdatePeople::create($params);
    }

    /**
     * Get the backend command to delete a Person
     *
     * @param array $params
     *
     * @return \Dvsa\Olcs\Transfer\Command\AbstractCommand
     */
    protected function getDeleteCommand($params): DeletePeople|DeletePeopleApplication
    {
        $params['id'] = $this->getApplicationId();
        return DeletePeople::create($params);
    }
}
