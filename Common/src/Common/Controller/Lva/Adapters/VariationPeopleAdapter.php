<?php

namespace Common\Controller\Lva\Adapters;

use Psr\Container\ContainerInterface;

class VariationPeopleAdapter extends AbstractPeopleAdapter
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

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
