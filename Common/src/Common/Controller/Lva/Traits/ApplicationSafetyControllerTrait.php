<?php

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Dvsa\Olcs\Transfer\Command\Application\DeleteWorkshop;
use Dvsa\Olcs\Transfer\Command\Application\UpdateSafety;
use Dvsa\Olcs\Transfer\Query\Application\Safety;
use Zend\Form\Form;

/**
 * Application Safety Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ApplicationSafetyControllerTrait
{
    protected function save($data, $partial)
    {
        $dtoData = $data['application'];
        $dtoData['id'] = $this->getApplicationId();
        $dtoData['partial'] = $partial;
        $dtoData['licence'] = $data['licence'];
        $dtoData['licence']['id'] = $this->getLicenceId();

        return $this->handleCommand(UpdateSafety::create($dtoData));
    }

    protected function deleteWorkshops($ids)
    {
        $data = [
            'application' => $this->getApplicationId(),
            'ids' => $ids
        ];

        return $this->handleCommand(DeleteWorkshop::create($data));
    }

    /**
     * Get Safety Data
     *
     * @return array
     */
    protected function getSafetyData()
    {
        $response = $this->handleQuery(Safety::create(['id' => $this->getApplicationId()]));

        if (!$response->isOk()) {
            return $this->notFoundAction();
        }

        $application = $response->getResult();

        $this->canHaveTrailers = $application['canHaveTrailers'];
        $this->hasTrailers = $application['hasTrailers'];
        $this->workshops = $application['licence']['workshops'];

        return $application;
    }

    /**
     * Alter the form depending on the LVA type
     *
     * @param \Zend\Form\Form
     */
    protected function alterFormForLva(Form $form)
    {

    }
}
